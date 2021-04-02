<?php

namespace AltSolution\Admin\Form;

abstract class AbstractFactory implements FactoryInterface
{
    /**
     * @var BuilderInterface
     */
    private $builder;
    /**
     * @var array
     */
    private $provided = [];

    /**
     * AbstractFactory constructor.
     * @param BuilderInterface $builder
     */
    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    public function create($dataSource = null)
    {
        if ($dataSource !== null) {
            $this->builder->setDataSource($dataSource);
        }
        $fields = $this->buildForm($this->builder);
        $this->builder->addMany($fields);

        return $this->builder->build();
    }

    public function provide($name, $value)
    {
        $this->provided[$name] = $value;

        return $this;
    }

    public function provideMany(array $values)
    {
        foreach ($values as $key => $value) {
            $this->provided[$key] = $value;
        }

        return $this;
    }

    public function provided($name, $default = null)
    {
        return array_get($this->provided, $name, $default);
    }

    /**
     * Build form using form builder
     * @param BuilderInterface $builderInterface
     */
    abstract protected function buildForm(BuilderInterface $builderInterface);
}