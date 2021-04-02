<?php namespace AltSolution\Admin\System;

class Menu implements \IteratorAggregate
{
    /**
     * @var MenuSection[]
     */
    private $sections = [];

    /**
     * @param MenuSection $section
     */
    public function addSection(MenuSection $section)
    {
        $alias = $section->getAlias();
        if (empty($alias)) {
            $alias = uniqid('section-', true);
        }
        $this->sections[$alias] = $section;
    }

    /**
     * @return MenuSection[]
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @param string $alias
     * @return MenuSection
     * @throws Exception
     */
    public function getSection($alias)
    {
        if (!array_key_exists($alias, $this->sections)) {
            throw new Exception('Section not defined: ' . $alias);
        }

        return $this->sections[$alias];
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->sections);
    }
}