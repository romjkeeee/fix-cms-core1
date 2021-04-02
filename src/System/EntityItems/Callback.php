<?php namespace AltSolution\Admin\System\EntityItems;

use AltSolution\Admin\System\EntityItem;

class Callback extends EntityItem
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * Route constructor.
     * @param string $description
     * @param callable $callback
     */
    public function __construct($description, $callback)
    {
        parent::__construct($description);

        $this->callback = $callback;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return '*';
    }

    public function isMultiple()
    {
        return true;
    }

    private $cacheOptions = null;

    public function getOptions()
    {
        if ($this->cacheOptions === null)
        {
            $options = call_user_func($this->callback);
            $options = array_filter($options, function($el) {
               return ($el instanceof Select\Option);
            });

            $this->cacheOptions = $options;
        }

        return $this->cacheOptions;
    }
}