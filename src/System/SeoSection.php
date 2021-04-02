<?php

namespace AltSolution\Admin\System;

class SeoSection implements \IteratorAggregate
{
    private $name;
    private $description;
    /**
     * @var SeoField[]
     */
    private $fields = [];
    private $weight = 0;

    public function __construct($name, $description)
    {
        $this->name = $name;
        $this->description = $description;
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
    public function getDescription()
    {
        return $this->description;
    }

    public function addField(SeoField $field)
    {
        $this->fields[] = $field;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->fields);
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return int
     */
    public function hasFields()
    {
        return count($this->fields);
    }

    /**
     * @return SeoField[]
     */
    public function getFields()
    {
        return $this->fields;
    }
}