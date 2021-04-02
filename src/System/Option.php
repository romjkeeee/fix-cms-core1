<?php namespace AltSolution\Admin\System;

class Option implements \IteratorAggregate
{
    /**
     * @var OptionSection[]
     */
    private $sections = [];

    public function addSection(OptionSection $section)
    {
        $alias = uniqid('section-', true);
        $this->sections[$alias] = $section;
    }

    /**
     * @return OptionSection[]
     */
    public function getSections()
    {
        return $this->sections;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->sections);
    }
}