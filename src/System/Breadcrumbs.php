<?php namespace AltSolution\Admin\System;

class Breadcrumbs implements \IteratorAggregate
{
    /**
     * @var BreadcrumbsItem[]
     */
    private $items = [];

    public function addItem(BreadcrumbsItem $item)
    {
        $this->items[] = $item;
    }

    /**
     * @return BreadcrumbsItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    public function getIterator()
    {
        foreach ($this->items as $item)
        {
            yield $item;
        }
    }
}