<?php

namespace AltSolution\Admin\System;

class SeoField
{
    private $name;
    private $description;
    private $help;
    private $type = 'text';

    public function __construct($name, $description, $help = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->help = $help;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setHelp($str)
    {
        $this->help = $str;
    }

    public function getHelp()
    {
        return $this->help;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }
}