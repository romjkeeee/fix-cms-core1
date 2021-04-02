<?php

namespace AltSolution\Admin\Modules;

use AltSolution\Admin\Auth\TwoFactorAuthInterface;
use AltSolution\Admin\System\AbstractModule;
use AltSolution\Admin\System\MenuBuilder;
use AltSolution\Admin\System\PermissionBuilder;
use Illuminate\Routing\Router;

class UserModule extends AbstractModule
{
    public function buildMenu(MenuBuilder $menu)
    {
        $menu->addSection(trans('admin::user.ms_users'), 'fa-users')
            ->setAlias('users')->setWeight(100);
        $menu->addItem(trans('admin::user.mi_profile'), route('admin::user_profile'))
            ->setPermission('user.edit');
        $menu->addItem(trans('admin::user.mi_list'), route('admin::user_list'))
            ->setPermission('user.list');
        $menu->addItem(trans('admin::user.mi_roles'), route('admin::user_roles'))
            ->setPermission('role.list');
        $menu->addItem(trans('admin::user.mi_acl'), route('admin::user_acl'))
            ->setPermission('role.edit');
    }

    public function buildPermissions(PermissionBuilder $builder)
    {
        $builder->addSection('admin::user.ps_users');
        $builder->addPermission('user.list', 'admin::user.p_user_list');
        $builder->addPermission('user.edit', 'admin::user.p_user_edit');
        $builder->addPermission('user.delete', 'admin::user.p_user_delete');
        $builder->addPermission('user.impersonate', 'admin::user.p_user_impersonate');

        // TODO: weak. provokes class initialization
        $requireSecret = app(TwoFactorAuthInterface::class)->requireSecret();
        // if required manage secret codes
        if ($requireSecret) {
            $builder->addPermission('user.2fa', 'admin::user.p_user_2fa');
        }

        $builder->addSection('admin::user.ps_roles');
        $builder->addPermission('role.list', 'admin::user.p_role_list');
        $builder->addPermission('role.edit', 'admin::user.p_role_edit');
        $builder->addPermission('role.delete', 'admin::user.p_role_delete');
    }

    public function registerRoutes(Router $router)
    {
        // todo: switch to post, split acl and roles from users

        $config = app();

        $userController = $config['admin.controller.user'];
        $router->get('users/profile', $userController.'@getProfile')->name('user_profile');
        $router->get('users', $userController.'@getIndex')->name('user_list');
        $router->get('users/edit/{id?}', $userController.'@getEdit')->name('user_edit');
        $router->post('users/save', $userController.'@postSave')->name('user_save');
        $router->post('users/action', $userController.'@action')->name('user_action');

        $tfaController = $config['admin.controller.2fa'];
        $router->get('users/2fa/{id}', $tfaController.'@index')->name('user_2fa');
        $router->post('users/2fa/{id}', $tfaController.'@action');

        $aclController = $config['admin.controller.acl'];
        $router->get('users/acl', $aclController.'@getAcl')->name('user_acl');
        $router->post('users/acl', $aclController.'@postAcl');

        $roleController = $config['admin.controller.role'];
        $router->get('users/roles', $roleController.'@getRoles')->name('user_roles');
        $router->get('users/roles/edit/{id?}', $roleController.'@getEdit')->name('user_editrole');
        $router->post('users/roles', $roleController.'@postRoles');
        $router->post('users/roles/action', $roleController.'@action')->name('role_action');
    }
}