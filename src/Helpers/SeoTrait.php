<?php

namespace AltSolution\Admin\Helpers;

use AltSolution\Admin\Events\SeoModelDeleted;
use AltSolution\Admin\Events\SeoModelUpdated;
use AltSolution\Admin\Models\SeoModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Model[] $seoModelProperties
 */
trait SeoTrait
{
    // TODO: extract to repository

    public function seoFields()
    {
        return config('seo.default_fields');
    }

    public function seoAttributes($locale)
    {
        $cacheKey = 'seo_model_' . $this->getTable() . '_' . $this->getKey();

        return \Cache::remember($cacheKey, config('seo.cache_ttl'), function () use ($locale) {
            $fields = $this->seoFields();
            $properties = $this->seoModelProperties1($locale);
            $propArr = [];
            foreach ($properties as $prop) {
                $propArr[$prop->locale][$prop->key] = $prop->value;
            }

            $all = [];
            $locales = cms_locales();
            foreach ($locales as $locale) {
                $all[$locale] = [];
                $props = array_get($propArr, $locale);
                if (!is_array($props)) {
                    continue;
                }
                foreach ($props as $key => $value) {
                    if (!isset($fields[$key])) {
                        continue;
                    }
                    $all[$locale][$key] = $value;
                }
            }

            return $all;
        });
    }

    public function seoSave(array $values = null)
    {
        if ($values === null) {
            return;
        }

        $fields = $this->seoFields();
        $properties = $this->seoModelProperties;
        $locales = cms_locales();

        $editFields = [];
        foreach ($fields as $name => $options) {
            if (!array_get($options, 'edit', true)) {
                // not editable
                continue;
            }
            $editFields[] = $name;
        }

        $updatedIds = [];
        $changed = false;

        // create or update
        foreach ($locales as $locale) {
            foreach ($editFields as $field) {
                $value = array_get($values, $locale.'.'.$field);

                /** @var SeoModel $newProp */
                $newProp = SeoModel::query()->firstOrNew([
                    'model_id' => $this->getKey(),
                    'model_name' => $this->getTable(),
                    'locale' => $locale,
                    'key' => $field,
                ]);
                $newProp->key = $field;
                $newProp->value = $value;
                if (!$newProp->exists || $newProp->isDirty('value')) {
                    $changed = true;
                }
                $newProp->save();

                $updatedIds[] = $newProp->getKey();
            }
        }

        // delete
        foreach ($properties as $property) {
            if (in_array($property->getKey(), $updatedIds)) {
                continue;
            }

            $property->delete();
        }

        if ($changed) {
            $cacheKey = 'seo_model_' . $this->getTable() . '_' . $this->getKey();
            \Cache::forget($cacheKey);
            event(new SeoModelUpdated($this));
        }
    }

    public function seoDelete()
    {
        $properties = $this->seoModelProperties;
        foreach ($properties as $prop) {
            $prop->delete();
        }

        $cacheKey = 'seo_model_' . $this->getTable() . '_' . $this->getKey();
        \Cache::forget($cacheKey);
        event(new SeoModelDeleted($this));
    }

    protected function seoModelProperties()
    {
        return $this->hasMany(SeoModel::class, 'model_id')->where('model_name', $this->getTable());
    }
}
