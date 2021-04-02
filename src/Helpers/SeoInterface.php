<?php

namespace AltSolution\Admin\Helpers;

interface SeoInterface
{
    /**
     * Get the value of the primary key
     *
     * @return mixed
     */
    function getKey();
    /**
     * Get the table associated
     *
     * @return string
     */
    function getTable();

    /**
     * Save/Update model SEO
     * @param array|null $values
     * @return bool
     */
    function seoSave(array $values = null);
    /**
     * Delete model SEO
     * @return bool
     */
    function seoDelete();
    /**
     * Get model SEO attributes
     * @return array
     */
    function seoAttributes();
    /**
     * Get model SEO fields
     * @return array
     */
    function seoFields();
}
