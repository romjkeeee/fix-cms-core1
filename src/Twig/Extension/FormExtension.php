<?php

namespace AltSolution\Admin\Twig\Extension;

use AltSolution\Admin\Form\Form;
use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

class FormExtension extends Twig_Extension
{
    public function getName()
    {
        return 'AltSolution_Admin_Form';
    }

    public function getFunctions()
    {
        return [
            // todo: @deprecated
            new Twig_SimpleFunction('form_component', [$this, 'form_component'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('form_field', [$this, 'form_field'], ['is_safe' => ['html']]),

            new Twig_SimpleFunction('form_open', [$this, 'form_open'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('form_close', [$this, 'form_close'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('form_submit', [$this, 'form_submit'], ['is_safe' => ['html']]),

            new Twig_SimpleFunction('html_attributes', [$this, 'html_attributes'], ['is_safe' => ['html']]),
        ];
    }

    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('html_inline', [$this, 'html_inline'], ['is_safe' => ['html']]),
        ];
    }

    public function form_component()
    {
        $args = func_get_args();
        $form = array_shift($args);
        return call_user_func_array([$form, 'renderComponent'], $args);
    }

    public function form_field()
    {
        $args = func_get_args();
        $form = array_shift($args);
        return call_user_func_array([$form, 'renderField'], $args);
    }

    public function form_open(Form $form, array $options = [])
    {
        return $form->renderComponent('form_open', $options);
    }

    public function form_close(Form $form)
    {
        return $form->renderComponent('form_close');
    }

    public function form_submit(Form $form, array $options = [])
    {
        return $form->renderComponent('form_submit', $options);
    }

    public function html_inline($value)
    {
        // remove multiple spaces, newlines
        // $value = preg_replace('~\n+~u', '', $value);
        $value = preg_replace('~\s+~u', ' ', $value);

        return trim($value);
    }

    public function html_attributes(array $attributes)
    {
        $html = [];
        foreach ($attributes as $key => $value) {
            $element = $this->attribute($key, $value);
            if (! is_null($element)) {
                $html[] = $element;
            }
        }
        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }

    protected function attribute($key, $value)
    {
        // required, readonly, etc.
        if (is_numeric($key)) {
            return $value;
        }
        // Treat boolean attributes as HTML properties
        if (is_bool($value) && $key != 'value') {
            return $value ? $key : '';
        }
        if (! is_null($value)) {
            return $key . '="' . e($value) . '"';
        }
    }

}
