<?php

namespace AltSolution\Admin\Modules;

use AltSolution\Admin\System\AbstractModule;
use AltSolution\Admin\System\MenuBuilder;
use AltSolution\Admin\System\PermissionBuilder;
use Illuminate\Routing\Router;

class HomeReviewModule extends AbstractModule
{
    public function buildMenu(MenuBuilder $menu)
    {
        $menu->addSection('Home Reviews', 'fa-bars', route('admin::home_reviews_list'))
            ->setPermission('menu.list')
            ->setAlias('home-reviews')
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
            $router->get('{code}/home_review', 'HomeReviewController@index')->name('home_reviews_list');
        });
    }
}