<?php

namespace AltSolution\Admin\Form\Field;

use AltSolution\Admin\Form\AbstractField;

class Checkbox extends AbstractField
{
    protected $view = 'checkbox';
    protected $rules = ['boolean'];
}