<?php

namespace AltSolution\Admin\Form\Field;

use AltSolution\Admin\Form\AbstractField;

class Email extends AbstractField
{
    protected $view = 'email';
    protected $rules = [
        'email'
    ];
}