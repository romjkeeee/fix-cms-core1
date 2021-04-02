<?php

namespace AltSolution\Admin\Providers;

use AltSolution\Admin\Forms\UserForm;
use AltSolution\Admin\Http\Controllers;
use AltSolution\Admin\Models\User;
use AltSolution\Admin\Modules\User\UserFormInterface;
use AltSolution\Admin\Modules\User\UserModelInterface;
use Illuminate\Support\ServiceProvider;

class UserProvider extends ServiceProvider
{
    protected $defer = true;

    public function provides()
    {
        return [
            UserFormInterface::class,
            UserModelInterface::class,
            'admin.controller.user',
            'admin.controller.acl',
            'admin.controller.role',
            'admin.controller.2fa',
        ];
    }

    public function register()
    {
        $this->app->bind(UserFormInterface::class, UserForm::class);
        $this->app->bind(UserModelInterface::class, User::class);
        //$this->app->alias(Cache::class, 'cache');
        $this->app->instance('admin.controller.user', Controllers\UsersController::class);
        $this->app->instance('admin.controller.acl', Controllers\AclController::class);
        $this->app->instance('admin.controller.role', Controllers\RoleController::class);
        $this->app->instance('admin.controller.2fa', Controllers\TFAController::class);
    }
}
