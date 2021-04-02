<?php

namespace AltSolution\Admin\Seo;

use AltSolution\Admin\Events\SeoPageUpdated;
use AltSolution\Admin\Models\SeoPage;

class SeoRepository implements SeoRepositoryInterface
{
    /**
     * @param string|null $page
     * @return SeoPage[]
     */
    private function pageProperties($page = null)
    {
        $q = SeoPage::query();
        if ($page !== null) {
            $q->where('page', $page);
        }
        $properties = $q->get();

        return $properties;
    }

    public function getPageProperties($name)
    {
        $cacheKey = 'seo_page_' . $name;

        return \Cache::remember($cacheKey, config('seo.cache_ttl'), function () use ($name) {
            $properties = $this->pageProperties($name);

            $data = [];
            foreach ($properties as $prop) {
                $data[$prop->locale][$prop->key] = $prop->value;
            }

            return $data;
        });
    }

    public function getPagesProperties()
    {
        $properties = $this->pageProperties();
        $data = [];
        foreach ($properties as $prop) {
            $data[$prop->page][$prop->locale][$prop->key] = $prop->value;
        }

        return $data;
    }

    public function setPagesProperties(array $properties)
    {
        $oldProperties = $this->pageProperties();

        $updatedIds = [];
        foreach ($properties as $page => $propsByLocale) {

            $changed = false;
            foreach ($propsByLocale as $locale => $props) {
                foreach ($props as $propKey => $propValue) {

                    $newProp = SeoPage::query()->firstOrNew([
                        'page' => $page,
                        'locale' => $locale,
                        'key' => $propKey,
                    ]);
                    $newProp->value = $propValue;
                    if (!$newProp->exists || $newProp->isDirty('value')) {
                        $changed = true;
                    }
                    $newProp->save();

                    $updatedIds[] = $newProp->getKey();
                }
            }

            if ($changed) {
                $cacheKey = 'seo_page_' . $page;
                \Cache::forget($cacheKey);
                event(new SeoPageUpdated($page));
            }
        }

        foreach ($oldProperties as $property) {
            if (in_array($property->getKey(), $updatedIds)) {
                continue;
            }
            $property->delete();
        }
    }

}
