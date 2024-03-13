<?php

namespace AltSolution\Admin\Modules;

use AltSolution\Admin\System\AbstractModule;
use AltSolution\Admin\System\MenuBuilder;
use AltSolution\Admin\System\PermissionBuilder;
use Illuminate\Routing\Router;

class RibbonModule extends AbstractModule
{
    public function buildMenu(MenuBuilder $menu)
    {
        $menu->addSection('Whats-new', 'fa-bars', route('admin::ribbon_list'))
            ->setPermission('menu.list')
            ->setAlias('ribbon')
            ->setWeight(300);

    }

    public function buildPermissions(PermissionBuilder $builder)
    {

        $builder->addSection('admin::menu.ps_menu');
        $builder->addPermission('menu.list', 'admin::menu.p_menu_list');
//        $builder->addPermission('menu.edit', 'admin::menu.p_menu_edit');
//        $builder->addPermission('menu.delete', 'admin::menu.p_menu_delete');
//        $builder->addPermission('menuitem.list', 'admin::menu.p_menu_item_list');
//        $builder->addPermission('menuitem.edit', 'admin::menu.p_menu_item_edit');
//        $builder->addPermission('menuitem.delete', 'admin::menu.p_menu_item_delete');
    }

    public function registerRoutes(Router $router)
    {
        $namespace = 'AltSolution\Admin\Http\Controllers';
        $router->group(['namespace' => $namespace], function(Router $router)
        {
            $router->get('ribbon', 'RibbonController@getIndex')->name('ribbon_list');
//            $router->get('menu/edit/{id?}', 'MenuController@getEdit')->name('menu_edit');
//            $router->post('menu/save', 'MenuController@postSave')->name('menu_save');
//            $router->post('menu/action', 'MenuController@action')->name('menu_action');
//
//            $router->get('menu/items/{id?}', 'MenuController@getItems')->name('menu_items');
//            $router->get('menu/edititem/{id?}/{id2?}', 'MenuController@getEdititem')->name('menu_edititem');
//            $router->post('menu/saveitem', 'MenuController@postSaveitem')->name('menu_saveitem');
//            $router->get('menu/deleteitem/{id?}', 'MenuController@getDeleteitem')->name('menu_deleteitem');
//            $router->post('menu/massitemdelete', 'MenuController@postMassitemdelete')->name('menu_massitemdelete');
//
//            $router->post('menu/sortitems', 'MenuController@postSortItems')->name('menu_sortitems');
        });
    }
}