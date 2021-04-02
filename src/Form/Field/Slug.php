<?php

namespace AltSolution\Admin\Form\Field;

use AltSolution\Admin\Form\AbstractField;

class Slug extends AbstractField
{
    protected $view = 'slug';

    protected function viewData(array $options)
    {
        $source = $this->transformName($this->getOption('source'));
        $enable = $this->normalizeBoolean($this->getOption('enable', false));
        if (!$enable) {
            $source = null;
        }
        return parent::viewData($options) + compact('source');
    }
}