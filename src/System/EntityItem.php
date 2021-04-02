<?php namespace AltSolution\Admin\System;

abstract class EntityItem
{
    /**
     * @var string
     */
    private $description;
    /**
     * @var int
     */
    private $weight = 0;

    /**
     * EntityItem constructor.
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
     * @return string
     */
    abstract public function getUrl();

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     * @return EntityItem
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * @param string $url
     * @return bool
     */
    public function match($url)
    {
        if (!$this->isMultiple()) {
            return $url == $this->getUrl();
        }
        
        foreach ($this->getOptions() as $option) {
            if ($option->match($url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Entity provides several urls
     * @return bool
     */
    public function isMultiple()
    {
        return false;
    }

    /**
     * Get entity urls
     * @return EntityItems\Select\Option[]
     */
    public function getOptions()
    {
        return [];
    }

}