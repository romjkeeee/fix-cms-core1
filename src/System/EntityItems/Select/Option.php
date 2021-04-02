<?php namespace AltSolution\Admin\System\EntityItems\Select;

class Option
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
     * Option constructor.
     * @param string $description
     * @param string $url
     */
    public function __construct($description, $url)
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

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return bool
     */
    public function match($url)
    {
        return $this->url == $url;
    }

}