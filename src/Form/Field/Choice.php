<?php

namespace AltSolution\Admin\Form\Field;

use AltSolution\Admin\Form\AbstractField;

class Choice extends AbstractField
{
    use ChoicesTrait;

    protected $view = 'choice';

    protected function viewData(array $options)
    {
        $data = parent::viewData($options);
        $choices = $this->getChoices();

        $multiple = $this->getOption('multiple', false);

        return $data + compact('choices', 'multiple');
    }
}
