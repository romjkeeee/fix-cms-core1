<?php

namespace AltSolution\Admin\Form\Field;

use AltSolution\Admin\Form\AbstractField;

class Partial extends AbstractField
{
    protected $view = 'partial';

    protected function viewData(array $options)
    {
        $data = parent::viewData($options);

        $content = $this->getOption('content', '');
        if (!is_string($content)) {
            $content = (string)$content;
        }

        return $data + compact('content');
    }
}
