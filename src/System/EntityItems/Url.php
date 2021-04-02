<?php namespace AltSolution\Admin\System\EntityItems;

use AltSolution\Admin\System\EntityItem;

class Url extends EntityItem
{
    /**
     * @var string
     */
    private $url;

    /**
     * Route constructor.
     * @param string $description
     * @param $url
     */
    public function __construct($description, $url)
    {
        parent::__construct($description);

        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}