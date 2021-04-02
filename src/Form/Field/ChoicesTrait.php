<?php

namespace AltSolution\Admin\Form\Field;

use Traversable;

trait ChoicesTrait
{
    protected function getChoices()
    {
        if (!isset($this->options['choices'])) {
            $baseClassName = substr(strrchr(get_class($this), '\\'), 1);
            throw new \Exception($baseClassName . ' field must have choices');
        }

        $choices_raw = $this->options['choices'];

        if (is_callable($choices_raw)) {
            return call_user_func($choices_raw);
        } elseif (is_array($choices_raw)) {
            return $choices_raw;
        } elseif ($choices_raw instanceof Traversable) {
            return iterator_to_array($choices_raw);
        } else {
            throw new \Exception('Select field must have valid choices');
        }
    }
}
