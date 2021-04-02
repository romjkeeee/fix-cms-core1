<?php

namespace AltSolution\Admin\Helpers;

/**
 * Use on models to add multilangual support
 * Class TranslateTrait
 * @package AltSolution\Admin\Helpers
 */
trait TranslateTrait
{
    /**
     * Override to add support for multilingual
     * @return array
     */
    public function getFillable()
    {
        $fillable = [];
        foreach (parent::getFillable() as $field) {
            if (strpos($field, '*') === false) {
                $fillable[] = $field;
            } else {
                foreach (cms_locales() as $locale) {
                    $fillable[] = str_replace('*', $locale, $field);
                }
            }
        }

        return $fillable;
    }

    /**
     * Translate model property
     * @param string $property
     * @param string $locale
     * @return string
     */
    public function trans($property, $locale = null)
    {
        if ($locale === null) {
            $locale = app('config')->get('app.locale');
        }
        $fallbackLocale = app('config')->get('app.fallback_locale');

        $value = array_get($this, $property . '_' . $locale);
        if (empty($value)) {
            $value = array_get($this, $property . '_' . $fallbackLocale);
        }

        return $value;
    }

}
