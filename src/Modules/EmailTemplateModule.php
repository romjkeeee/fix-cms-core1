<?php namespace AltSolution\Admin\Modules;

use AltSolution\Admin\System\AbstractModule;
use AltSolution\Admin\System\MenuBuilder;
use AltSolution\Admin\System\PermissionBuilder;
use Illuminate\Routing\Router;

class EmailTemplateModule extends AbstractModule
{
    public function buildMenu(MenuBuilder $menu)
    {
        $menu->addSection(trans('admin::email_template.ms_email_templates'), 'fa-envelope-o', route('admin::email_template_list'))
            ->setPermission('email_template.list')
            ->setAlias('email_template')
            ->setWeight(200);
    }

    public function buildPermissions(PermissionBuilder $builder)
    {
        $builder->addSection('admin::email_template.ps_email_templates');
        $builder->addPermission('email_template.list', 'admin::email_template.p_email_template_list');
        $builder->addPermission('email_template.edit', 'admin::email_template.p_email_template_edit');
        $builder->addPermission('email_template.delete', 'admin::email_template.p_email_template_delete');
    }

    public function registerRoutes(Router $router)
    {
        $namespace = 'AltSolution\Admin\Http\Controllers';
        $router->group(['namespace' => $namespace], function(Router $router)
        {
            $router->get('email_template', 'EmailTemplateController@index')->name('email_template_list');
            $router->get('email_template/edit/{id}', 'EmailTemplateController@edit')->name('email_template_edit');
            $router->post('email_template/save', 'EmailTemplateController@save')->name('email_template_save');
        });
    }
}