<?php

namespace AltSolution\Admin\Modules;

use AltSolution\Admin\System\AbstractModule;
use AltSolution\Admin\System\MenuBuilder;
use AltSolution\Admin\System\PermissionBuilder;
use Illuminate\Routing\Router;

class PartnerModule extends AbstractModule
{
    public function buildMenu(MenuBuilder $menu)
    {
        $menu->addSection('Partners', 'fa-bars', route('admin::partners'))
            ->setPermission('menu.list')
            ->setAlias('partners')
            ->setWeight(300);

    }

    public function buildPermissions(PermissionBuilder $builder)
    {

        $builder->addSection('admin::menu.ps_menu');
        $builder->addPermission('menu.list', 'admin::menu.p_menu_list');
    }

    public function registerRoutes(Router $router)
    {
        $namespace = 'AltSolution\Admin\Http\Controllers';
        $router->group(['namespace' => $namespace], function(Router $router)
        {
            $router->get('patners', 'PartnerController@getIndex')->name('partner_list');
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