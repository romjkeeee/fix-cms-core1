<?php namespace AltSolution\Admin\System\EntityItems;

use AltSolution\Admin\System\EntityItem;

class Route extends EntityItem
{
    /**
     * @var
     */
    private $route;

    /**
     * Route constructor.
     * @param string $description
     * @param $route
     */
    public function __construct($description, $route)
    {
        parent::__construct($description);

        $this->route = $route;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return route($this->route, [], false);
    }
}