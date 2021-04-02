<?php

namespace AltSolution\Admin\System;

use Illuminate\Routing\Router;

trait ModuleTrait
{
    public function buildMenu(MenuBuilder $builder)
    {
        //
    }

    public function buildPermissions(PermissionBuilder $builder)
    {
        //
    }

    public function buildEntity(EntityBuilder $builder)
    {
        //
    }

    public function buildSeo(SeoBuilder $builder)
    {
        //
    }

    public function buildOptions(OptionBuilder $builder)
    {
        //
    }

    public function registerRoutes(Router $router)
    {
        //
    }
}
