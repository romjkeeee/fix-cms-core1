<?php namespace AltSolution\Admin\System;

class Seo implements \IteratorAggregate
{
    /**
     * @var SeoSection[]
     */
    private $sections = [];

    public function addSection(SeoSection $section)
    {
        $alias = uniqid('section-', true);
        $this->sections[$alias] = $section;
    }

    /**
     * @return SeoSection[]
     */
    public function getSections()
    {
        return $this->sections;
    }

    /*
    public function getAllFields()
    {
        $fields = [];
        
        foreach ($this->sections as $section) {
            foreach ($section as $item) {
                $fields[] = $item;
            }
        }
        
        return $fields;
    }
    */

    public function getIterator()
    {
        return new \ArrayIterator($this->sections);
    }
}