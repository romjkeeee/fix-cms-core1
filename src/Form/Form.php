<?php

namespace AltSolution\Admin\Form;

use IteratorAggregate;

class Form implements IteratorAggregate
{
    /** @var ComponentInterface[] */
    private $components = [];

    /** @var  mixed */
    private $dataSource;

    /**
     * Add component to form
     * @param ComponentInterface $component
     */
    public function add(ComponentInterface $component)
    {
        $this->components[$component->getName()] = $component;
        $component->setForm($this);
    }

    /**
     * Get component by name
     * @param string $name
     * @return ComponentInterface
     */
    public function get($name)
    {
        return $this->components[$name];
    }

    /**
     * Render form field
     * @param string $name
     * @param array $options
     * @return string
     * @throws \Exception
     */
    public function renderField($name, array $options = [])
    {
        /** @var FieldInterface $component */
        $component = $this->components[$name];
        if (!($component instanceof FieldInterface)) {
            throw new \Exception('Component is not field: ' . $name);
        }
        $component->setValue(array_get($this->dataSource, $name));

        return $component->render($options);
    }

    /**
     * Render form component
     * @param string $name
     * @param array $options
     * @return string
     */
    public function renderComponent($name, array $options = [])
    {
        $component = $this->components[$name];

        return $component->render($options);
    }

    /**
     * Set form field data source
     * @param mixed $dataSource
     */
    public function setDataSource($dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /**
     * Get any value from data source
     * @param string|null $key
     * @return mixed
     */
    public function getData($key = null)
    {
        if ($key === null) {
            return $this->dataSource;
        }
        return array_get($this->dataSource, $key);
    }

//    /**
//     * Call function of data source
//     * @param string $fn
//     * @param array $args
//     * @return mixed
//     */
//    public function callData($fn, array $args = [])
//    {
//        return call_user_func_array([$this->dataSource, $fn], $args);
//    }

    // --- IteratorAggregate

    public function getIterator()
    {
        return new \ArrayIterator($this->components);
    }

    /**
     * @return \Generator|FieldInterface[]
     */
    public function getFields()
    {
        foreach ($this->components as $component) {
            if ($component instanceof FieldInterface) {
                yield $component;
            }
        }
    }

    /**
     * @return array
     */
    public function getValidationRules()
    {
        $rules = [];
        foreach ($this->getFields() as $field) {
            $rules[$field->getName()] = $field->getValidationRules();
        }

        return $rules;
    }

    public function getValidationAttributes()
    {
        $attributes = [];
        foreach ($this->getFields() as $field) {
            $attributes[$field->getName()] = $field->getValidationAttribute();
        }

        return $attributes;
    }
}