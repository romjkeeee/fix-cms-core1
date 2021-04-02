<?php namespace AltSolution\Admin\System;

class BreadcrumbsItem
{
    /**
     * @var string
     */
    private $description;
    /**
     * @var string
     */
    private $url;

    /**
     * BreadcrumbsItem constructor.
     * @param string $description
     * @param string|null $url
     */
    public function __construct($description, $url = null)
    {
        $this->description = $description;
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function getUrl()
    {
        if ($this->url) {
            return $this->url;
        }
        
        return '#';
    }

}