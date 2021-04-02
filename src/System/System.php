<?php namespace AltSolution\Admin\System;

final class System
{
    /**
     * @var boolean
     */
    private $isBackend;
    /**
     * @var ModuleInterface[]
     */
    private $modules = [];
    /**
     * @var Permission
     */
    private $permission;
    /**
     * @var Entity
     */
    private $entity;
    /**
     * @var Layout
     */
    private $layout;
    /**
     * @var Seo
     */
    private $seo;
    /**
     * @var Option
     */
    private $option;

    /**
     * System constructor.
     * @param Permission $permission
     * @param Entity $entity
     * @param Layout $layout
     * @param Seo $seo
     * @param Option $option
     */
    public function __construct(Permission $permission, Entity $entity, Layout $layout, Seo $seo, Option $option)
    {
        $this->permission = $permission;
        $this->entity = $entity;
        $this->layout = $layout;
        $this->seo = $seo;
        $this->option = $option;
    }

    /**
     * @return bool
     */
    public function isBackend()
    {
        return $this->isBackend;
    }

    /**
     * @param bool $isBackend
     */
    public function setIsBackend($isBackend = true)
    {
        $this->isBackend = $isBackend;
    }
    
    /**
     * @return Permission
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return Layout
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @return Seo
     */
    public function getSeo()
    {
        return $this->seo;
    }

    /**
     * todo: lazy
     * @return Option
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * @param ModuleInterface[] $modules
     * @throws Exception
     */
    public function registerModules(array $modules)
    {
        foreach ($modules as $module)
        {
            if (!($module instanceof ModuleInterface)) {
                throw new Exception('Invalid module passed');
            }
            $this->modules[] = $module;
        }
    }

    public function bootModules()
    {
        $menuBuilder = new MenuBuilder();
        $permissionBuilder = new PermissionBuilder();
        $entityBuilder = new EntityBuilder();
        $seoBuilder = new SeoBuilder();
        $optionBuilder = new OptionBuilder();

        foreach ($this->modules as $module) {
            $module->buildMenu($menuBuilder);
            $module->buildPermissions($permissionBuilder);
            $module->buildEntity($entityBuilder);
            $module->buildSeo($seoBuilder);
            $module->buildOptions($optionBuilder);
        }
        
        $menuBuilder->checkPermission();
        $menuBuilder->sort();
        $menu = $this->layout->getMenu();
        foreach ($menuBuilder->getSections() as $section) {
            $menu->addSection($section);
        }

        $permissionBuilder->sort();
        foreach ($permissionBuilder->getSections() as $section) {
            $this->permission->addSection($section);
        }
        
        $entityBuilder->sort();
        foreach ($entityBuilder->getItems() as $item) {
            $this->entity->addItem($item);
        }

        $seoBuilder->sort();
        foreach ($seoBuilder->getSections() as $section) {
            $this->seo->addSection($section);
        }

        $optionBuilder->sort();
        foreach ($optionBuilder->getSections() as $section) {
            $this->option->addSection($section);
        }
    }

}