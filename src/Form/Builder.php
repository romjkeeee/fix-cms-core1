<?php

namespace AltSolution\Admin\Form;

use Traversable;

class Builder implements BuilderInterface
{
    /**
     * @var string
     */
    private $prefix;
    /**
     * @var ComponentInterface[]
     */
    private $components = [];
    /**
     * @var bool
     */
    private $locked = false;
    /**
     * @var mixed
     */
    private $dataSource;

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function setDataSource($dataSource)
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    public function getDataSource()
    {
        return $this->dataSource;
    }

    public function build()
    {
        $form = new Form();

        foreach ($this->components as $componentName => $componentData) {
            list($componentClass, $componentOptions) = $componentData;
            $name = $this->prefix . $componentName;
            $component = new $componentClass($name, $componentOptions);

            $form->add($component);
        }

        if ($this->dataSource) {
            $form->setDataSource($this->dataSource);
        }

        $this->locked = true;

        return $form;
    }

    public function add($name, $component, array $options = [])
    {
        if ($this->locked) {
            throw new \Exception('Builder locked after getting form');
        }
        $this->components[$name] = [$component, $options];

        return $this;
    }

    public function remove($name)
    {
        if ($this->locked) {
            throw new \Exception('Builder locked after getting form');
        }
        unset($this->components[$name]);

        return $this;
    }

    function addMany($fields)
    {
        if (is_array($fields) || ($fields instanceof Traversable)) {
            foreach ($fields as $name => $options) {
                $type = array_get($options, 'type');
                if ($type === null) {
                    throw new \Exception('Field type is undefined: ' . $name);
                }
                unset($options['type']);
                if (is_numeric($name)) {
                    call_user_func([$this, $type], $options);
                } else {
                    call_user_func([$this, $type], $name, $options);
                }
            }
        }

        return $this;
    }

    public function form(array $options = [])
    {
        return $this
            ->add('form_open', Component\FormOpen::class, $options)
            ->add('form_submit', Component\FormSubmit::class, $options)
            ->add('form_close', Component\FormClose::class);
    }

    function csrfToken()
    {
        return $this->add('_token', Field\CsrfToken::class);
    }

    function hidden($name)
    {
        return $this->add($name, Field\Hidden::class);
    }

    function checkbox($name, array $options = [])
    {
        return $this->add($name, Field\Checkbox::class, $options);
    }

    function date($name, array $options = [])
    {
        return $this->add($name, Field\Date::class, $options);
    }

    function datetime($name, array $options = [])
    {
        return $this->add($name, Field\DateTime::class, $options);
    }

    function email($name, array $options = [])
    {
        return $this->add($name, Field\Email::class, $options);
    }

    function password($name, array $options = [])
    {
        return $this->add($name, Field\Password::class, $options);
    }

    function select($name, array $options = [])
    {
        return $this->add($name, Field\Select::class, $options);
    }

    function selectModel($name, array $options = [])
    {
        return $this->add($name, Field\SelectModel::class, $options);
    }

    function text($name, array $options = [])
    {
        return $this->add($name, Field\Text::class, $options);
    }

    function slug($name, array $options = [])
    {
        return $this->add($name, Field\Slug::class, $options);
    }

    function textarea($name, array $options = [])
    {
        return $this->add($name, Field\Textarea::class, $options);
    }

    function wysiwyg($name, array $options = [])
    {
        return $this->add($name, Field\Wysiwyg::class, $options);
    }

    function image($name, array $options = [])
    {
        return $this->add($name, Field\Image::class, $options);
    }

    function imageUrl($name, array $options = [])
    {
        return $this->add($name, Field\ImageUrl::class, $options);
    }

    function thumbnail($name, array $options = [])
    {
        return $this->add($name, Field\Thumbnail::class, $options);
    }

    function upload($name, array $options = [])
    {
        return $this->add($name, Field\Upload::class, $options);
    }

    function choice($name, array $options = [])
    {
        return $this->add($name, Field\Choice::class, $options);
    }

    function partial($name, array $options = [])
    {
        return $this->add($name, Field\Partial::class, $options);
    }
}
