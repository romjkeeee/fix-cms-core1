<?php

namespace AltSolution\Admin\Form;

interface FactoryInterface
{
    /**
     * Build and return form
     * @param mixed $dataSource
     * @return Form
     */
    public function create($dataSource = null);

    /**
     * Provide custom value to factory
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function provide($name, $value);

    /**
     * Provide custom data to factory
     * @param array $values
     * @return $this
     */
    public function provideMany(array $values);

    /**
     * Get builder custom provided data
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function provided($name, $default = null);
}