<?php namespace AltSolution\Admin\System;

class EntityBuilder
{
    /**
     * @var EntityItem[]
     */
    private $items = [];


    /**
     * @param string $description
     * @return EntityItems\Custom
     */
    public function addCustom($description)
    {
        $item = new EntityItems\Custom($description);
        $this->items[] = $item;

        return $item;
    }

    /**
     * @param string $description
     * @param string $route
     * @return EntityItem
     */
    public function addRoute($description, $route)
    {
        $item = new EntityItems\Route($description, $route);
        $this->items[] = $item;
        
        return $item;
    }

    /**
     * @param string $description
     * @param string $url
     * @return EntityItems\Url
     */
    public function addUrl($description, $url)
    {
        $item = new EntityItems\Url($description, $url);
        $this->items[] = $item;

        return $item;
    }

    /**
     * @param string $description
     * @param callable $callback
     * @return EntityItems\Callback
     */
    public function addCallback($description, $callback)
    {
        $item = new EntityItems\Callback($description, $callback);
        $this->items[] = $item;

        return $item;
    }

    /**
     * @param string $description
     * @param string $model
     * @param string $route
     * @return EntityItems\Model
     */
    public function addModel($description, $model, $route)
    {
        $item = new EntityItems\Model($description, $model, $route);
        $this->items[] = $item;

        return $item;
    }

    /**
     * @return EntityItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Perform section soring based on it weight
     */
    public function sort()
    {
        usort($this->items, function(EntityItem $a, EntityItem $b) {
            if ($a->getWeight() == $b->getWeight()) {
                return 0;
            }
            return ($a->getWeight() > $b->getWeight()) ? -1 : 1;
        });
    }
}