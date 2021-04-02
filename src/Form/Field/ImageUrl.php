<?php

namespace AltSolution\Admin\Form\Field;

use AltSolution\Admin\Form\AbstractField;

class ImageUrl extends AbstractField
{
    protected $view = 'image';

    protected function viewData(array $options)
    {
        $data = parent::viewData($options);
        $empty = array_get($data['options'], 'empty', true);
        $empty = $this->normalizeBoolean($empty);
        if ($this->value) {
            $data['name_delete'] = $this->transformName($this->name . '_delete');
        }
        $image = $this->value;

        return $data + compact('image', 'empty');
    }

}