<?php

namespace AltSolution\Admin\Modules;

use AltSolution\Admin\System\AbstractModule;
use AltSolution\Admin\System\EntityBuilder;
use AltSolution\Admin\System\MenuBuilder;
use AltSolution\Admin\System\PermissionBuilder;
use Illuminate\Routing\Router;

class ContentModule extends AbstractModule
{

    public function buildMenu(MenuBuilder $menu)
    {
        $menu->addSection(trans('admin::content.ms_content'), 'fa-file-text-o', route('admin::content_list'))
            ->setPermission('content.list')
            ->setAlias('content')
            ->setWeight(400);
    }

    public function buildPermissions(PermissionBuilder $builder)
    {
        $builder->addSection('admin::content.ps_content');
        $builder->addPermission('content.list', 'admin::content.p_content_list');
        $builder->addPermission('content.edit', 'admin::content.p_content_edit');
        $builder->addPermission('content.delete', 'admin::content.p_content_delete');
    }

    public function buildEntity(EntityBuilder $builder)
    {
        $builder->addUrl('admin::content.me_main', '/')
            ->setWeight(100);
        $builder->addCustom('admin::content.me_custom')
            ->setWeight(-100);
    }

    public function registerRoutes(Router $router)
    {
        $controller = app('admin.controller.content');

        $router->get('content', $controller . '@getIndex')->name('content_list');
        $router->get('content/edit/{id?}', $controller . '@getEdit')->name('content_edit');
        $router->post('content/save', $controller . '@postSave')->name('content_save');
        $router->post('content/action', $controller . '@action')->name('content_action');
    }

}