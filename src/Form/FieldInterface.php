<?php

namespace AltSolution\Admin\Form;

interface FieldInterface extends ComponentInterface
{
    /**
     * @param mixed $value
     */
    public function setValue($value);

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return array
     */
    public function getValidationRules();

    /**
     * @return array
     */
    public function getValidationAttribute();
}
