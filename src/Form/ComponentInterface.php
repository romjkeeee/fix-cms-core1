<?php

namespace AltSolution\Admin\Form;

interface ComponentInterface
{
    /**
     * Get component Name attribute
     * @return string
     */
    public function getName();

    /**
     * Set relation with form
     * @param Form $form
     */
    public function setForm(Form $form);

    /**
     * Get form related with
     * @return Form
     */
    public function getForm();

    /**
     * Retrieve field option
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getOption($name, $default = null);

    /**
     * Set field option
     * @param string $name
     * @param mixed $value
     */
    public function setOption($name, $value);

    /**
     * Render component
     * @param array $options
     * @return string
     */
    public function render(array $options = []);
}