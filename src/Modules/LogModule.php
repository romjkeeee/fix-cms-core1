<?php namespace AltSolution\Admin\Modules;

use AltSolution\Admin\System\AbstractModule;
use AltSolution\Admin\System\MenuBuilder;
use AltSolution\Admin\System\PermissionBuilder;
use Illuminate\Routing\Router;

class LogModule extends AbstractModule
{
    /**
     * Get menu sections for display in side menu
     * @param MenuBuilder $menu
     */
    public function buildMenu(MenuBuilder $menu)
    {
        //
    }

    /**
     * Build permissions for access control
     * @param PermissionBuilder $builder
     */
    public function buildPermissions(PermissionBuilder $builder)
    {
        $builder->addSection('admin::log.ps_logs');
        $builder->addPermission('logs', 'admin::log.p_logs');
    }

    public function registerRoutes(Router $router)
    {
        $namespace = 'AltSolution\Admin\Http\Controllers';
        $router->group(['namespace' => $namespace], function(Router $router)
        {
            $router->get('logs', 'LogsController@index')->name('logs_list');
            $router->get('logs/{file}', 'LogsController@view')->name('logs_view');
        });
    }
}