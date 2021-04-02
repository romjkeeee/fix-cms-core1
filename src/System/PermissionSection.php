<?php namespace AltSolution\Admin\System;

class PermissionSection implements \IteratorAggregate
{
    /**
     * @var string
     */
    private $description;
    /**
     * @var PermissionItem[]
     */
    private $permissions = [];
    /**
     * @var integer
     */
    private $weight = 0;

    /**
     * PermissionSection constructor.
     * @param string $description
     */
    public function __construct($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param PermissionItem $permission
     */
    public function addItem(PermissionItem $permission)
    {
        $this->permissions[] = $permission;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->permissions);
    }

    /**
     * @return integer
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     * @return $this
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return int
     */
    public function hasPermissions()
    {
        return count($this->permissions);
    }

    /**
     * @return PermissionItem[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

}