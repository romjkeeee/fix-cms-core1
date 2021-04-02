<?php

namespace AltSolution\Admin\Form\Field;

use Illuminate\Database\Eloquent\Model;

class SelectModel extends Select
{
    protected function getChoices()
    {
        if (!isset($this->options['model'])) {
            throw new \Exception('SelectModel field must have model');
        }

        $modelClass = $this->options['model'];
        /** @var Model $model */
        $model = new $modelClass;

        $pk = $this->getOption('primary_key', 'id');
        $tk = $this->getOption('title_key', 'title');
        $nullable = $this->getOption('nullable', false);

        return iterator_to_array($this->generateOptions($model, $pk, $tk, $nullable));
    }

    /**
     * @param Model $model
     * @param string|callable $pk
     * @param string|callable $tk
     * @param bool $nullable
     * @return \Generator
     */
    private function generateOptions($model, $pk, $tk, $nullable)
    {
        if ($nullable) {
            $nullTitle = array_get($this->options, 'placeholder');
            yield null => $nullTitle;
        }
        foreach ($model->all() as $item) {
            $key = is_callable($pk) ? call_user_func($pk, $item) : $item[$pk];
            $value = is_callable($tk) ? call_user_func($tk, $item) : $item[$tk];
            yield $key => $value;
        }
    }

}