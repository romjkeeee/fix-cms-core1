<?php

namespace AltSolution\Admin\Seo;

use AltSolution\Admin\Helpers\SeoInterface;

interface SeoManagerInterface
{
    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getDefaultTitle();

    /**
     * @param string $title
     * @return $this
     */
    public function setDefaultTitle($title);

    /**
     * @return string
     */
    public function getPriorTitle();

    /**
     * @param string $title
     * @return $this
     */
    public function setPriorTitle($title);

    /**
     * @return string
     */
    public function getSiteName();

    /**
     * @return string
     */
    public function getDefaultSiteName();

    /**
     * @param string $name
     * @return $this
     */
    public function setDefaultSiteName($name);

    /**
     * @return string
     */
    public function getPriorSiteName();

    /**
     * @param string $name
     * @return $this
     */
    public function setPriorSiteName($name);

    /**
     * @param string $name
     * @return string
     */
    public function getMetaName($name);

    /**
     * @param string $name
     * @return string
     */
    public function getDefaultMetaName($name);

    /**
     * @param string $name
     * @param string $content
     * @return $this
     */
    public function setDefaultMetaName($name, $content);

    /**
     * @param string $name
     * @return string
     */
    public function getPriorMetaName($name);

    /**
     * @param string $name
     * @param string $content
     * @return $this
     */
    public function setPriorMetaName($name, $content);

    /**
     * @param string $property
     * @return string
     */
    public function getMetaProperty($property);

    /**
     * @param string $property
     * @return string
     */
    public function getDefaultMetaProperty($property);

    /**
     * @param string $property
     * @param string $content
     * @return $this
     */
    public function setDefaultMetaProperty($property, $content);

    /**
     * @param string $property
     * @return string
     */
    public function getPriorMetaProperty($property);

    /**
     * @param string $property
     * @param string $content
     * @return $this
     */
    public function setPriorMetaProperty($property, $content);

    /**
     * @param string $key
     * @return string
     */
    public function getCustom($key);

    /**
     * @param string $key
     * @return string
     */
    public function getDefaultCustom($key);

    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setDefaultCustom($key, $value);

    /**
     * @param string $key
     * @return string
     */
    public function getPriorCustom($key);

    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setPriorCustom($key, $value);

    /**
     * Reset all current settings to defaults
     * @return $this
     */
    public function reset();

    /**
     * @return string
     */
    public function renderAll();

    /**
     * @param SeoInterface $model
     * @param string|null $locale
     * @return string
     */
    public function renderFieldset(SeoInterface $model, $locale = null);

    /**
     * Read seo settings from page
     * @param string $pageName
     * @param string|null $locale
     * @return void
     */
    public function fromPage($pageName, $locale = null);

    /**
     * Read seo setting from model
     * @param SeoInterface $model
     * @return void
     */
    public function fromModel(SeoInterface $model);

    /**
     * Set replace pairs to generate dynamic seo
     * @param array $pairs
     * @return $this
     */
    public function replacePairs(array $pairs);
}