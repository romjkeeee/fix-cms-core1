<?php namespace AltSolution\Admin\Modules;

use AltSolution\Admin\System\AbstractModule;
use AltSolution\Admin\System\MenuBuilder;
use AltSolution\Admin\System\OptionBuilder;
use AltSolution\Admin\System\PermissionBuilder;
use AltSolution\Admin\System\SeoBuilder;
use Illuminate\Routing\Router;

class CoreModule extends AbstractModule
{
    public function buildMenu(MenuBuilder $builder)
    {
        $builder->addSection(trans('admin::debug.ms_name'), 'fa-bug')
            ->setAlias('debug')
            ->setWeight(-101);
        $builder->addItem(trans('admin::debug.mi_email_test'), route('admin::email_test'))
            ->setPermission('debug');
        $builder->addItem(trans('admin::debug.mi_phpinfo'), route('admin::phpinfo'))
            ->setPermission('debug');
        $builder->addItem(trans('admin::debug.mi_php'), route('admin::php'))
            ->setPermission('debug');
        $builder->addItem(trans('admin::debug.mi_sql'), route('admin::sql'))
            ->setPermission('debug');
        $builder->addItem(trans('admin::debug.mi_artisan'), route('admin::artisan'))
            ->setPermission('debug');
    }

    public function buildPermissions(PermissionBuilder $builder)
    {
        $builder->addSection('admin::core.ps_core')
            ->setWeight(-100);
        $builder->addPermission('admin', 'admin::core.p_admin');
        $builder->addPermission('debug', 'admin::core.p_debug');
        $builder->addPermission('cache', 'admin::core.p_cache');
    }

    public function buildSeo(SeoBuilder $builder)
    {
        $builder->addSection('cms_common', 'admin::seo.ss_common')
            ->setWeight(100);
        $builder->addField('site_name', 'admin::seo.sf_common_name');
        $builder->addDefaultFields();
    }

    public function buildOptions(OptionBuilder $builder)
    {
        $builder->addSection('admin', 'admin::option.os_admin')
            ->setWeight(100);
        $builder->addField('email', [
            'type' => 'email',
            'label' => trans('admin::option.of_admin_email'),
            'help' => trans('admin::option.oh_admin_email'),
            'required' => true,
        ]);
        $builder->addField('email_d', [
            'type' => 'text',
            'label' => trans('admin::option.of_admin_email_d'),
            'help' => trans('admin::option.oh_admin_email_d'),
        ]);
        $builder->addField('start_page', [
            'type' => 'select',
            'label' => trans('admin::option.of_admin_start_page'),
            'help' => trans('admin::option.oh_admin_start_page'),
            'choices' => function () {
                // todo: refactoring extract method
                $menu = cms_system()->getLayout()->getMenu();
                $result = [];
                foreach ($menu->getSections() as $section) {
                    $options = [];
                    if ($section->hasItems()) {
                        foreach ($section->getItems() as $item) {
                            $options[$item->getRelativeLink()] = $item->getName();
                        }
                    } else {
                        $options[$section->getRelativeLink()] = $section->getName();
                    }
                    $result[$section->getName()] = $options;
                }
                return $result;
            },
        ]);
    }

    public function registerRoutes(Router $router)
    {
        $namespace = 'AltSolution\Admin\Http\Controllers';
        $router->group(['namespace' => $namespace], function(Router $router)
        {
            $router->get('/', 'IndexController@index')->name('index');

            $router->get('elfinder', 'ElFinderController@index')->name('elfinder');
            $router->get('elfinder/connector', 'ElFinderController@connect')->name('elfinder_connector');
            $router->post('elfinder/connector', 'ElFinderController@connect');

            $router->get('cache', 'CoreController@getCache')->name('cache');
            $router->post('cache', 'CoreController@postCache');

            $router->get('debug/email_test', 'DebugController@emailTest')->name('email_test');
            $router->post('debug/email_test', 'DebugController@emailTestPost')->name('email_test_post');
            $router->get('debug/php', 'DebugController@php')->name('php');
            $router->post('debug/php', 'DebugController@php');
            $router->get('debug/phpinfo', 'DebugController@phpinfo')->name('phpinfo');
            $router->get('debug/sql', 'DebugController@sql')->name('sql');
            $router->post('debug/sql', 'DebugController@sql');
            $router->get('debug/artisan', 'DebugController@artisan')->name('artisan');
            $router->post('debug/artisan/rpc', 'DebugController@artisanRpc')->name('artisan_rpc');
        });
    }
}