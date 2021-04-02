<?php

namespace AltSolution\Admin\Option;

use AltSolution\Admin\Models\Option;

class Repository implements RepositoryInterface
{
    private $options = null;

    /**
     * @return array|null
     */
    private function getOptions()
    {
        if ($this->options === null) {
            $this->options = \Cache::remember('cms_options', config('admin.option_cache_ttl'), function () {
                return Option::all()
                    ->pluck('value', 'name')
                    ->all();
            });
        }

        return $this->options;
    }

    public function get($name)
    {
        $options = $this->getOptions();

        return array_get($options, $name);
    }

    public function getAll()
    {
        $options = $this->getOptions();

        return $options;
    }

    public function set($name, $value)
    {
        $option = Option::query()->firstOrNew(compact('name'));
        $option['value'] = $value;
        $option->save();

        \Cache::forget('cms_options');
        $this->options = null;
    }

    public function setMany(array $options)
    {
        foreach ($options as $name => $value) {
            $option = Option::query()->firstOrNew(compact('name'));
            $option['value'] = $value;
            $option->save();
        }

        \Cache::forget('cms_options');
        $this->options = null;
    }
}