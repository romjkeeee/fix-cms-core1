<?php namespace AltSolution\Admin\System;

class Permission implements \IteratorAggregate
{
    /**
     * @var PermissionSection[]
     */
    private $sections = [];

    /**
     * @param PermissionSection $section
     */
    public function addSection(PermissionSection $section)
    {
        $alias = uniqid('section-', true);
        $this->sections[$alias] = $section;
    }

    /**
     * @return PermissionSection[]
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @return PermissionItem[]
     */
    public function getAllPermissions()
    {
        $permissions = [];

        foreach ($this->sections as $section) {
            foreach ($section as $item) {
                $permissions[] = $item;
            }
        }

        return $permissions;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->sections);
    }
}