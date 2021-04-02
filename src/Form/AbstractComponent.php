<?php

namespace AltSolution\Admin\Form;

abstract class AbstractComponent implements ComponentInterface
{
    protected $viewNs = 'admin::form.component';
    protected $view = 'empty';

    /**
     * @var string
     */
    protected $name;
    /**
     * @var array
     */
    protected $options;
    /**
     * @var Form
     */
    private $form;

    /**
     * @param string $name
     * @param array $options
     */
    public function __construct($name, array $options = [])
    {
        $this->name = $name;
        $this->options = $options;
    }

    public function setForm(Form $form)
    {
        $this->form = $form;
    }

    public function getForm()
    {
        return $this->form;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Get initial option
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        return array_get($this->options, $name, $default);
    }

    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * Build data for view
     * @param array $options
     * @return array
     */
    protected function viewData(array $options)
    {
        $form = $this->form;
        $field = $this;

        $options = array_merge($this->options, $options);
        $id = array_get($options, 'id', 'id_' . str_random());
        $name = $this->transformName($this->name);

        // default label
        if (empty($options['label'])) {
            $options['label'] = $this->name;
        }

        return compact('form', 'field', 'id', 'name', 'options');
    }

    /**
     * Generate name for html. Example: foo.bar.baz ==> foo[bar][baz]
     * @param $name
     * @return string
     */
    protected function transformName($name)
    {
        $nameParts = explode('.', $name);
        $nameTmp = [];
        $first = true;
        foreach ($nameParts as $namePart) {
            if ($first) {
                $first = false;
                $nameTmp[] = $namePart;
            } else {
                $nameTmp[] = '[' . $namePart . ']';
            }
        }
        return implode('', $nameTmp);
    }

    public function render(array $options = [])
    {
        $viewData = $this->viewData($options);
        $viewFile = $this->viewNs . '.' . $this->view;

        return view($viewFile, $viewData)->render();
    }
}