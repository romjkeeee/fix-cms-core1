<?php

namespace AltSolution\Admin\Form\Field;

use AltSolution\Admin\Form\AbstractField;
use DateTimeInterface;

class DateTime extends AbstractField
{
    protected $view = 'datetime';
    protected $format = 'Y-m-d H:i:s';

    public function getValidationRules()
    {
        $rules = parent::getValidationRules();
        $rules[] = 'date_format:' . $this->getFormat();
        return $rules;
    }

    protected function getFormat()
    {
        return $this->getOption('format', $this->format);
    }

    public function normalizeValue($value)
    {
        if ($value === null) {
            return null;
        }

        $format = $this->getFormat();

        if ($value instanceof DateTimeInterface) {
            // we think value is DateTime or Carbon instance
            return $value->format($format);
        }
        elseif (is_numeric($value)) {
            // we think value is timestamp
            return date($format, $value);
        }
        else {
            // we think wtf, maybe string
            return date($format, strtotime($value));
        }
    }
}