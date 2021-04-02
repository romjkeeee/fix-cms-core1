<?php

namespace AltSolution\Admin\Option;

interface RepositoryInterface
{
    /**
     * Get option by name
     * @param string $name
     * @return mixed
     */
    public function get($name);

    /**
     * Get all options
     * @return array|null
     */
    public function getAll();

    /**
     * Set option
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value);

    /**
     * Set many options
     * @param array $options
     */
    public function setMany(array $options);
}