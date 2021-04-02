<?php

namespace AltSolution\Admin\Form;


abstract class AbstractField extends AbstractComponent implements FieldInterface
{
    protected $viewNs = 'admin::form.field';

    protected $rules = [];

    /**
     * @var mixed
     */
    protected $value;

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getValidationRules()
    {
        $rules = $this->rules;

        $required = $this->normalizeBoolean($this->getOption('required'));
        if ($required) {
            $rules[] = 'required';
        }

        return $rules;
    }

    public function getValidationAttribute()
    {
        return $this->getOption('label', $this->name);
    }

    /**
     * Modify value for displaying
     * @param $value
     * @return mixed
     */
    public function normalizeValue($value)
    {
        return $value;
    }

    /**
     * Parse boolean conditions
     * @param bool|string $required
     * @return bool
     * @throws \Exception
     */
    protected function normalizeBoolean($required)
    {
        if (is_bool($required)) {
            return $required;
        }
        elseif (is_string($required)) {
            list($condition, $conditionArgs) = explode(':', $required);
            $conditionArgsParts = explode(',', $conditionArgs);

            $fieldName = array_shift($conditionArgsParts);
            $fieldValue = $this->getForm()->get($fieldName)->getValue();

            switch ($condition)
            {
                case 'if':
                    if (count($conditionArgsParts) > 1) {
                        $result = in_array($fieldValue, $conditionArgsParts);
                    } elseif (count($conditionArgsParts) == 1) {
                        $result = $fieldValue == $conditionArgsParts[0];
                    } else {
                        $result = (bool)$fieldValue;
                    }
                    break;
                case 'unless':
                    if (count($conditionArgsParts) > 1) {
                        $result = !in_array($fieldValue, $conditionArgsParts);
                    } elseif (count($conditionArgsParts) == 1) {
                        $result = $fieldValue != $conditionArgsParts[0];
                    } else {
                        $result = !$fieldValue;
                    }
                    break;
                default:
                    throw new \Exception('Unknown required condition: '.$condition);
            }

            return $result;
        }
        else {
            return (bool)$required;
        }
        // TODO: is_callable
    }

    protected function viewData(array $options)
    {
        $data = parent::viewData($options);

        $value = array_get($options, 'value', $this->value);
        $value = $this->normalizeValue($value);

        $required = array_get($data['options'], 'required', false);
        $required = $this->normalizeBoolean($required);

        $readonly = array_get($data['options'], 'readonly', false);
        $readonly = $this->normalizeBoolean($readonly);

        $disabled = array_get($data['options'], 'disabled', false);
        $disabled = $this->normalizeBoolean($disabled);

        return $data + compact('required', 'readonly', 'disabled', 'value');
    }
}