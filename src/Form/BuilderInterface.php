<?php

namespace AltSolution\Admin\Form;

use Traversable;

interface BuilderInterface
{
    /**
     * @return Form
     */
    function build();

    /**
     * @return string
     */
    public function getPrefix();

    /**
     * @param string $prefix
     * @return static
     */
    public function setPrefix($prefix);

    /**
     * Set form field data source
     * @param mixed $dataSource
     * @return static
     */
    function setDataSource($dataSource);

    /**
     * Get form field data source
     * @return mixed
     */
    function getDataSource();

    /**
     * Add component to the form
     * @param string $name
     * @param string $component
     * @param array $options
     * @return static
     * @throws \Exception
     */
    function add($name, $component, array $options = []);

    /**
     * Remove component from the form
     * @param $name
     * @return static
     */
    function remove($name);

    /**
     * @param array|Traversable $fields
     * @return static
     */
    function addMany($fields);

    function form(array $options = []);
    function csrfToken();
    function hidden($name);
    function checkbox($name, array $options = []);
    function date($name, array $options = []);
    function datetime($name, array $options = []);
    function email($name, array $options = []);
    function password($name, array $options = []);
    function select($name, array $options = []);
    function selectModel($name, array $options = []);
    function text($name, array $options = []);
    function slug($name, array $options = []);
    function textarea($name, array $options = []);
    function wysiwyg($name, array $options = []);
    function image($name, array $options = []);
    function imageUrl($name, array $options = []);
    function thumbnail($name, array $options = []);
    function upload($name, array $options = []);
    function choice($name, array $options = []);
    function partial($name, array $options = []);
}