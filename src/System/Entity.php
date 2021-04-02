<?php namespace AltSolution\Admin\System;

class Entity implements \IteratorAggregate
{
    /**
     * @var EntityItem[]
     */
    private $items = [];

    /**
     * @param EntityItem $item
     */
    public function addItem(EntityItem $item)
    {
        $this->items[] = $item;
    }

    /**
     * @return EntityItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }
}