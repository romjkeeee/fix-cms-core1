<?php

namespace AltSolution\Admin\Form\Field;

use AltSolution\Admin\Form\AbstractField;

class Select extends AbstractField
{
    use ChoicesTrait;

    protected $view = 'select';

    protected function viewData(array $options)
    {
        $data = parent::viewData($options);
        $choices = $this->getChoices();

        return $data + compact('choices');
    }

}