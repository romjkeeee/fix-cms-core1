<?php namespace AltSolution\Admin\System\EntityItems;

use AltSolution\Admin\System\EntityItem;

class Model extends EntityItem
{
    /**
     * @var string
     */
    private $model;
    /**
     * @var string
     */
    private $route;
    /**
     * @var string
     */
    private $titleKey = 'title';
    /**
     * @var string
     */
    private $routeKey = 'permalink';
    /**
     * @var string
     */
    private $modelKey = 'permalink';
    /**
     * @var string
     */
    private $activeKey;

    /**
     * Route constructor.
     * @param string $description
     * @param string $model
     * @param string $route
     */
    public function __construct($description, $model, $route)
    {
        parent::__construct($description);
        $this->model = $model;
        $this->route = $route;
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
        if ($this->cacheOptions === null) {
            /** @var \Illuminate\Database\Eloquent\Model $model */
            $model = app($this->model);
            $items = $model->all();

            $options = [];
            foreach ($items as $item) {
                if ($this->activeKey && isset($item[$this->activeKey]) && !$item[$this->activeKey]) {
                    // skip not active items
                    continue;
                }

                $titleKey = $this->titleKey;
                $localeSuffix = '_' . config('app.locale');
                if (isset($item[$titleKey . $localeSuffix])) {
                    $titleKey .= $localeSuffix;
                }

                $description = $item[$titleKey];
                $url = route($this->route, [$this->routeKey => $item[$this->modelKey]], false);

                $options[] = new Select\Option($description, $url);
            }

            $this->cacheOptions = $options;
        }

        return $this->cacheOptions;
    }

    /**
     * Which field is for title in model
     * @param string $titleKey
     * @return Model
     */
    public function setTitleKey($titleKey)
    {
        $this->titleKey = $titleKey;
        return $this;
    }

    /**
     * Which param is for permalink in route
     * @param string $routeKey
     * @return Model
     */
    public function setRouteKey($routeKey)
    {
        $this->routeKey = $routeKey;
        return $this;
    }

    /**
     * Which field is for permalink in model
     * @param string $modelKey
     * @return Model
     */
    public function setModelKey($modelKey)
    {
        $this->modelKey = $modelKey;
        return $this;
    }

}