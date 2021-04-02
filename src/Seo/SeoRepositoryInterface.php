<?php

namespace AltSolution\Admin\Seo;

interface SeoRepositoryInterface
{
    /**
     * Get properties for specific page
     * @param string $name
     * @return array
     */
    public function getPageProperties($name);

    /**
     * Get properties for all pages
     * @return array
     */
    public function getPagesProperties();

    /**
     * Update properties for all pages
     * @param array $properties
     * @return void
     */
    public function setPagesProperties(array $properties);
}
