<?php

namespace AltSolution\Admin;

use AltSolution\Admin\Acl;
use AltSolution\Admin\Form;
use AltSolution\Admin\Http\Controllers\Controller as BaseController;
use AltSolution\Admin\Http\Middleware\AuthenticateAdmin;
use AltSolution\Admin\Http\Middleware\BootSystem;
use AltSolution\Admin\System;
use Cviebrock\ImageValidator\ImageValidatorServiceProvider;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;

// TODO: @new_version rename to AdminServiceProvider
class AltSolutionAdminProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ .'/../config/admin.php', 'admin');

        $this->registerSystem();
        $this->registerModules();
        $this->registerContracts();

        // TODO: @new_version twig and image move here
        //$this->app->register(ImageValidatorServiceProvider::class);

        // todo: check modules registered
        //$this->app->register(Providers\Deferred::class);
        $this->app->register(Providers\SeoProvider::class);
        $this->app->register(Providers\ContentProvider::class);
        $this->app->register(Providers\UserProvider::class);
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootRoutes();
        $this->bootViews();

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'admin');

        $this->bootPolicies();
        if ($this->app->runningInConsole()) {
            $this->bootPublishes();
        }


//        /** @var DispatcherContract $events */
//        $events = $this->app->make(DispatcherContract::class);
//        $events->listen();
//        $events->subscribe();
    }

    private function bootRoutes()
    {
        $app = $this->app;

        if (!$app->routesAreCached()) {
            /** @var Router $router */
            $router = $app->make(Router::class);

            $router->middleware('admin.auth', AuthenticateAdmin::class);
            $router->middleware('system.boot', BootSystem::class);
            $router->middlewareGroup('admin', ['web', 'admin.auth', 'system.boot']);
            // MAIN CYCLE:
            //[request]->
            //  [middleware]web->[middleware]admin.auth->[middleware]system.boot->
            //      [router]->[controller]->[constructor]permission:admin->[action]permission:*

            $prefix = $app->make('config')->get('admin.admin_url', 'admin');
            $namespace = 'AltSolution\Admin\Http\Controllers';
            $router->group(['namespace' => $namespace], function (Router $router) use ($prefix) {
                $router->get('/vendor/admin/{path}', 'AssetController@index')->where('path', '.+');
                // auth routes
                $router->group([
                    'middleware' => 'web',
                    'prefix' => $prefix,
                    'as' => 'admin::'
                ], function (Router $router) {
                    $router->get('login', 'Auth\AuthController@getLogin')->name('login');
                    $router->post('login', 'Auth\AuthController@login');
                    $router->get('logout', 'Auth\AuthController@logout')->name('logout');

                    $router->get('password/reset/{token?}', 'Auth\PasswordController@showResetForm')->name('password_reset');
                    $router->post('password/email', 'Auth\PasswordController@sendResetLinkEmail')->name('password_email');
                    $router->post('password/reset', 'Auth\PasswordController@reset');

                    $router->get('login/verify', 'Auth\F2AController@verify')->name('2fa');
                    $router->post('login/verify', 'Auth\F2AController@verifyPost');

                    $router->get('impersonate/start/{id}', 'Auth\ImpersonateController@start')->name('impersonate');
                    $router->get('impersonate/stop', 'Auth\ImpersonateController@stop')->name('impersonate_stop');
                });
            });

            /** @var System\ModuleInterface[] $modules */
            $modules = $app->tagged('admin.system.module');
            $router->group([
                'middleware' => 'admin',
                'prefix' => $prefix,
                'as' => 'admin::'
            ], function (Router $router) use ($modules) {
                foreach ($modules as $module) {
                    // TODO: routes registered on any request
                    $module->registerRoutes($router);
                }
            });
        }
    }

    private function bootViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'admin');
        view()->composer('admin::layout', LayoutComposer::class);
    }

    private function bootPolicies()
    {
        /** @var Gate $gate */
        $gate = $this->app->make(Gate::class);
        /** @var Acl\AclRepositoryInterface $aclRepo */
        $aclRepo = $this->app->make(Acl\AclRepositoryInterface::class);
        $gate->define('permission', [$aclRepo, 'userHasPermission']);
    }

    private function bootPublishes()
    {
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/admin'),
        ], 'admin-views');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/admin'),
        ], 'admin-lang');

        $this->publishes([
            __DIR__ . '/../assets' => public_path('vendor/admin'),
        ], 'admin-assets');

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations')
        ], 'admin-migrations');

        // $this->publishes([
        //     $configFile => config_path('<some>.php'),
        // ], 'admin-config');
    }

    private function registerSystem()
    {
        $this->app->singleton(System\System::class);
        $this->app->singleton(System\Layout::class);

        // todo: not ok
        $this->app->bind(System\User::class, function (Application $app) {
            /** @var Models\User $user */
            $user = $app->make('auth')->user();
            if ($user === null) {
                return new System\User(0, null);
            }
            return new System\User($user['id'], $user->imageUrl('avatar_file', 'list'));
        });

        $this->app->afterResolving(BaseController::class, function (BaseController $controller, Application $app) {
            $controller->setLayout($app->make(System\Layout::class));
            $controller->boot();
            $controller->authorize('permission', 'admin');
        });

        // admin exception handler
        $previousHandler = null;
        if ($this->app->bound(ExceptionHandlerContract::class) === true) {
            $previousHandler = $this->app->make(ExceptionHandlerContract::class);
        }
        $this->app->singleton(ExceptionHandlerContract::class, function () use ($previousHandler) {
            return new \AltSolution\Admin\Exceptions\Handler($previousHandler);
        });
    }

    private function registerModules()
    {
        $modules = $this->app['config']['admin.modules'];
        $disabled = $this->app['config']['admin.modules_disabled'];
        foreach ($disabled as $alias) {
            unset($modules[$alias]);
        }
        foreach ($modules as $alias => $class) {
            $this->app->bind($alias, $class);
        }
        $this->app->tag(array_keys($modules), 'admin.system.module');
    }

    private function registerContracts()
    {
        // forms
        $this->app->bind(Form\BuilderInterface::class, Form\Builder::class);
        //$this->app->singleton(System\AssetInterface::class, System\Asset::class);

        // twig forms
        $this->app['config']->prepend('twigbridge.extensions.enabled', Twig\Extension\FormExtension::class);
        // twig cms
        $this->app['config']->prepend('twigbridge.extensions.enabled', Twig\Extension\HelpersExtension::class);

        // options
        $this->app->singleton(Option\RepositoryInterface::class, Option\Repository::class);

        // auth
        $this->app->bind(Auth\UserStoreInterface::class, Auth\SessionUserStore::class);
        if ($this->app['config']['admin.auth_2fa_google']) {
            $this->app->bind(Auth\QRCodeInterface::class, Auth\GoogleQRCode::class);
            $this->app->bind(Auth\TwoFactorAuthInterface::class, Auth\GoogleTFA::class);
        } else {
            $this->app->bind(Auth\TwoFactorAuthInterface::class, Auth\CodeTFA::class);
            $this->app->bind(Auth\TwoFactorCodeInterface::class, Auth\MailTFC::class);
        }
        // todo:
        //$this->app->alias(Auth\TwoFactorAuthInterface::class, 'admin.auth.2fa');
        //$this->app->alias(Auth\TwoFactorCodeInterface::class, 'admin.auth.2fa.code');

        // acl
        $this->app->singleton(Acl\AclRepositoryInterface::class, Acl\AclRepository::class);

        // email templates
        $this->app->singleton(EmailTemplate\MailerInterface::class, EmailTemplate\Mailer::class);
        $this->app->singleton(EmailTemplate\TemplateRepository::class);

        // for ImagesTrait
        $this->app->singleton(Image\ImageManagerInterface::class, function($app) {
            $config = $app['config'];
            // TODO: move to ci
            $options = [
                'directory' => $config['admin.upload_directory'],
                'url' => $config['admin.upload_url'],
                'permission' => $config['admin.upload_permission'],
                'keep_source' => $config['admin.upload_keep_source'],
                'double_encode' => $config['admin.upload_double_encode'],
                'slice_upload' => $config['admin.upload_slice_upload'],
                'slice_verbose' => $config['admin.upload_slice_verbose'],
            ];
            return new Image\ImageManager($options);
        });

        // for UploadsTrait
        $this->app->singleton(Upload\UploadManagerInterface::class, function($app) {
            $config = $app['config'];
            // TODO: move to ci
            $options = [
                'directory' => $config['admin.upload_directory'],
                'url' => $config['admin.upload_url'],
                'permission' => $config['admin.upload_permission'],
            ];
            return new Upload\UploadManager($options);
        });
    }

}
