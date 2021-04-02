<?php 

namespace AltSolution\Admin\System;

use Illuminate\Routing\Router;

interface ModuleInterface
{
    /**
     * Build menu for display in side menu
     * @param MenuBuilder $builder
     * @return void
     */
    public function buildMenu(MenuBuilder $builder);

    /**
     * Build permissions for access control
     * @param PermissionBuilder $builder
     * @return void
     */
    public function buildPermissions(PermissionBuilder $builder);

    /**
     * Get menu entities for menu edit
     * @param EntityBuilder $builder
     * @return void
     */
    public function buildEntity(EntityBuilder $builder);

    /**
     * Build entries for seo settings page
     * @param SeoBuilder $builder
     * @return void
     */
    public function buildSeo(SeoBuilder $builder);

    /**
     * Build entries for options settings page
     * @param OptionBuilder $builder
     * @return void
     */
    public function buildOptions(OptionBuilder $builder);

    /**
     * Register all required routes for module
     * @param Router $router
     * @return void
     */
    public function registerRoutes(Router $router);
}