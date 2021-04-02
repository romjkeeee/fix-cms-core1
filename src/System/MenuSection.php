<?php namespace AltSolution\Admin\System;

class MenuSection implements \IteratorAggregate
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $icon;
    /**
     * @var string
     */
    private $link;
    /**
     * @var string
     */
    private $permission;
    /**
     * @var MenuItem[]
     */
    private $items = [];
    /**
     * @var integer
     */
    private $counter = 0;
    /**
     * @var integer
     */
    private $weight = 0;
    /**
     * @var string
     */
    private $alias;
    /**
     * @var bool
     */
    private $active = false;

    /**
     * MenuSection constructor.
     * @param string $name
     * @param string $icon
     * @param string $link
     */
    public function __construct($name, $icon, $link = null)
    {
        $this->name = $name;
        $this->icon = $icon;
        $this->link = $link;
    }

    /**
     * @param MenuItem $item
     */
    public function addItem(MenuItem $item)
    {
        $this->items[] = $item;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @return int
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
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * @return bool
     */
    public function hasCounter()
    {
        return $this->counter > 0;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Used for selecting admin startup page
     * @return string
     */
    public function getRelativeLink()
    {
        return parse_url($this->link, PHP_URL_PATH);
    }

    /**
     * @param string $permission
     * @return MenuSection
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;
        return $this;
    }

    /**
     * @return string
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return int
     */
    public function hasItems()
    {
        return count($this->items);
    }

    /**
     * @return MenuItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     * @return $this
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    public function setActive($value)
    {
        $this->active = $value;
    }

    public function isActive()
    {
        return $this->active;
    }

    /**
     * Delete items by it index
     * @param array $toDelete
     */
    public function deleteItems(array $toDelete)
    {
        foreach ($toDelete as $index) {
            unset($this->items[$index]);
        }
    }

    /*
    public function isActive()
    {
        foreach ($this->items as $item) {
            if ($item->isActive()) {
                return true;
                break;
            }
        }

        return false;
    }
    */

}