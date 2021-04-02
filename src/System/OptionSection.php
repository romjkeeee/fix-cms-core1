<?php

namespace AltSolution\Admin\System;

use AltSolution\Admin\Form;

class OptionSection
{
    private $name;
    private $description;
    private $weight = 0;
    /**
     * @var Form\BuilderInterface
     */
    private $formBuilder;

    public function __construct(Form\BuilderInterface $formBuilder, $name, $description)
    {
        $this->formBuilder = $formBuilder;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return Form\BuilderInterface
     */
    public function getFormBuilder()
    {
        return $this->formBuilder;
    }
}