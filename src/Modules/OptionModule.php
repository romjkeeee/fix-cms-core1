<?php

namespace AltSolution\Admin\Modules;

use AltSolution\Admin\System\AbstractModule;
use AltSolution\Admin\System\MenuBuilder;
use AltSolution\Admin\System\PermissionBuilder;
use Illuminate\Routing\Router;

class OptionModule extends AbstractModule
{
    public function buildMenu(MenuBuilder $menu)
    {
        $menu->addSection(trans('admin::option.ms_options'), 'fa-cogs')
            ->setAlias('options')
            ->setWeight(-100);

        $menu->addItem(trans('admin::option.mi_core'), route('admin::options'))
            ->setPermission('options');
        $menu->addItem(trans('admin::option.mi_seo'), route('admin::seo'))
            ->setPermission('options');
        $menu->addItem(trans('admin::log.ms_logs'), route('admin::logs_list'))
            ->setPermission('logs');
        // todo:
        $menu->addItem(trans('admin::core.ms_cache'), route('admin::cache'))
            ->setPermission('cache');
    }

    public function buildPermissions(PermissionBuilder $builder)
    {
        $builder->addSection('admin::option.ps_options');
        $builder->addPermission('options', 'admin::option.p_options');
    }

    public function registerRoutes(Router $router)
    {
        $namespace = 'AltSolution\Admin\Http\Controllers';
        $router->group(['namespace' => $namespace], function(Router $router)
        {
            $router->get('options', 'OptionsController@getIndex')->name('options');
            $router->post('options', 'OptionsController@postIndex');
            $router->get('options/seo', 'OptionsController@getSeo')->name('seo');
            $router->post('options/seo', 'OptionsController@postSeo');
        });
    }
}