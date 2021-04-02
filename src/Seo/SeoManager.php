<?php

namespace AltSolution\Admin\Seo;

use AltSolution\Admin\Helpers\SeoInterface;

class SeoManager implements SeoManagerInterface
{
    protected static $metaNames = [
        'keywords',
        'description',
        //
        'robots',
        'revisit-after',
        'copyright',
        'document-state',
        'generator',
        'resource-type',
        // twitter
        'twitter:card',
        'twitter:site',
        'twitter:title',
        'twitter:description',
        'twitter:creator',
        'twitter:image:src',
        'twitter:domain',
    ];

    protected static $metaProperties = [
        // facebook
        'og:locale',
        'og:type',
        'og:url',
        'og:site_name',
        'og:title',
        'og:description',
        'og:image',
        'og:image:type',
        'og:image:width',
        'og:image:height',
        'og:see_also',
        'fb:app_id',
        'fb:admins',
        // google
        'place:location:latitude',
        'place:location:longitude',
        'business:contact_data:street_address',
        'business:contact_data:locality',
        'business:contact_data:postal_code',
        'business:contact_data:country_name',
        'business:contact_data:email',
        'business:contact_data:phone_number',
        'business:contact_data:website',
    ];

    /**
     * Consist all available levels
     * @var array
     */
    protected $levels = [
        'prior',        // ||
        'model',        // ||
        'page',         // ||
        'manual',       // ||
        'common',       // \/
    ];
    /**
     * Contains seo values splatted by levels
     * @var array
     */
    protected $data = [];
    /**
     * Contains dynamic seo values
     * @var array
     */
    protected $replacePairs = [];
    /**
     * @var SeoRepositoryInterface
     */
    private $seoRepo;

    public function __construct(SeoRepositoryInterface $seoRepo)
    {
        $this->seoRepo = $seoRepo;

        $this->reset();
    }

    protected function setData($key, $value, $level)
    {
        array_set($this->data, $level . '.' . $key, $value);
    }

    protected function getData($key, $level = null)
    {
        $value = null;

        if ($level !== null) {
            $value = array_get($this->data, $level . '.' . $key);
        } else {
            foreach ($this->levels as $level) {
                $value = array_get($this->data, $level . '.' . $key);
                if (!empty($value)) {
                    break;
                }
            }
        }

        return $value;
    }

    // ---

    public function getTitle()
    {
        return strtr($this->getData('title'), $this->replacePairs);
    }

    public function getDefaultTitle()
    {
        return $this->getData('title', 'manual');
    }

    public function setDefaultTitle($title)
    {
        $this->setData('title', $title, 'manual');

        return $this;
    }

    public function getPriorTitle()
    {
        return $this->getData('title', 'prior');
    }

    public function setPriorTitle($title)
    {
        $this->setData('title', $title, 'prior');

        return $this;
    }

    public function getSiteName()
    {
        return $this->getData('siteName');
    }

    public function getDefaultSiteName()
    {
        return $this->getData('siteName', 'manual');
    }

    public function setDefaultSiteName($name)
    {
        $this->setData('siteName', $name, 'manual');

        return $this;
    }

    public function getPriorSiteName()
    {
        return $this->getData('siteName', 'prior');
    }

    public function setPriorSiteName($name)
    {
        $this->setData('siteName', $name, 'prior');

        return $this;
    }

    public function getMetaName($name)
    {
        return strtr($this->getData('metaName.' . $name), $this->replacePairs);
    }

    public function getDefaultMetaName($name)
    {
        return $this->getData('metaName.' . $name, 'manual');
    }

    public function setDefaultMetaName($name, $content)
    {
        $this->setData('metaName.'.$name, $content, 'manual');

        return $this;
    }

    public function getPriorMetaName($name)
    {
        return $this->getData('metaName.' . $name, 'prior');
    }

    public function setPriorMetaName($name, $content)
    {
        $this->setData('metaName.'.$name, $content, 'prior');

        return $this;
    }

    public function getMetaProperty($property)
    {
        return strtr($this->getData('metaProperty.' . $property), $this->replacePairs);
    }

    public function getDefaultMetaProperty($property)
    {
        return $this->getData('metaProperty.' . $property, 'manual');
    }

    public function setDefaultMetaProperty($property, $content)
    {
        $this->setData('metaProperty.' . $property, $content, 'manual');

        return $this;
    }

    public function getPriorMetaProperty($property)
    {
        return $this->getData('metaProperty.' . $property, 'prior');
    }

    public function setPriorMetaProperty($property, $content)
    {
        $this->setData('metaProperty.' . $property, $content, 'prior');

        return $this;
    }

    public function getCustom($key)
    {
        return strtr($this->getData('custom.' . $key), $this->replacePairs);
    }

    public function getDefaultCustom($key)
    {
        return $this->getData('custom.' . $key, 'manual');
    }

    public function setDefaultCustom($key, $value)
    {
        $this->setData('custom.' . $key, $value, 'manual');

        return $this;
    }

    public function getPriorCustom($key)
    {
        return $this->getData('custom.' . $key, 'prior');
    }

    public function setPriorCustom($key, $value)
    {
        $this->setData('custom.' . $key, $value, 'prior');

        return $this;
    }

    public function reset()
    {
        // init data
        foreach ($this->levels as $level) {
            $this->data[$level] = [
                'siteName' => null,
                'title' => null,
                'metaName' => [],
                'metaProperty' => [],
                'custom' => [],
            ];
        }

        // TODO: IoC
        if (config('seo.enable_common')) {
            // load common settings
            $this->fromCommon();
        }

        return $this;
    }

    // ---

    protected function renderTitle()
    {
        $title = strtr($this->getData('title'), $this->replacePairs);
        $siteName = $this->getData('siteName');
        if (empty($siteName)) {
            return '<title>' . $title . '</title>';
        }
        return '<title>' . $title . config('seo.separator') . $siteName . '</title>';
    }

    protected function renderMetaName($name)
    {
        $content = strtr($this->getData('metaName.' . $name), $this->replacePairs);
        if (!empty($content)) {
            return '<meta name="' . $name . '" content="' . e($content) . '" />';
        }

        return null;
    }

    protected function renderMetaProperty($name)
    {
        $content = strtr($this->getData('metaProperty.' . $name), $this->replacePairs);
        if (!empty($content)) {
            return '<meta property="' . $name . '" content="' . e($content) . '" />';
        }

        return null;
    }

    public function renderAll()
    {
        $result = null;

        $result .= $this->renderTitle() . PHP_EOL;

        foreach (self::$metaNames as $name) {
            $value = $this->renderMetaName($name);
            if (!empty($value)) {
                $result .= $value . PHP_EOL;
            }
        }

        foreach (self::$metaProperties as $property) {
            $value = $this->renderMetaProperty($property);
            if (!empty($value)) {
                $result .= $value . PHP_EOL;
            }
        }

        return $result;
    }

    // ---

    protected function setAuto($key, $value, $level)
    {
        if ($key == 'site_name') {
            $this->setData('siteName', $value, $level);
        } elseif ($key == 'title') {
            $this->setData('title', $value, $level);
        } elseif (in_array($key, self::$metaNames)) {
            $this->setData('metaName.' . $key, $value, $level);
        } elseif (in_array($key, self::$metaProperties)) {
            $this->setData('metaProperty.' . $key, $value, $level);
        } else {
            $this->setData('custom.' . $key, $value, $level);
        }
    }

    protected function transProperties(array $properties, $locale)
    {
        $fallbackLocale = config('app.fallback_locale');
        $result = array_get($properties, $fallbackLocale, []);
        if ($locale != $fallbackLocale) {
            $localeProps = array_get($properties, $locale, []);
            foreach ($localeProps as $key => $value) {
                if (!empty($value)) {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }

    protected function fromCommon()
    {
        $locale = config('app.locale');
        $pageName = 'cms_common';
        $i18nProps = $this->seoRepo->getPageProperties($pageName);
        $props = $this->transProperties($i18nProps, $locale);
        foreach ($props as $key => $value) {
            $this->setAuto($key, $value, 'common');
        }
    }

    public function fromPage($pageName, $locale = null)
    {
        if ($locale === null) {
            $locale = config('app.locale');
        }
        $i18nProps = $this->seoRepo->getPageProperties($pageName);
        $props = $this->transProperties($i18nProps, $locale);
        foreach ($props as $key => $value) {
            $this->setAuto($key, $value, 'page');
        }
    }

    public function fromModel(SeoInterface $model, $locale = null)
    {
        if ($locale === null) {
            $locale = config('app.locale');
        }

        $i18nProps = $model->seoAttributes();
        $props = $this->transProperties($i18nProps, $locale);
        foreach ($props as $key => $value) {
            $this->setAuto($key, $value, 'model');
        }
    }

//    public function fromArray(array $props)
//    {
//    }

    public function replacePairs(array $pairs)
    {
        $this->replacePairs = $pairs;

        return $this;
    }

    public function renderFieldset(SeoInterface $model, $locale = null)
    {
        if ($locale === null) {
            $locale = config('app.locale');
        }

        $fields = $model->seoFields();
        $values = $model->seoAttributes();

        $form = cms_construct_form(function () use ($locale, $fields, $values) {
            foreach ($fields as $name => $options) {
                $fieldName = sprintf('seo.%s.%s', $locale, $name);
                yield $fieldName => [
                    // todo: more types
                    'type' => 'text',
                    'label' => trans(array_get($options, 'label')),
                ];
            }
        });

        $form->setDataSource(['seo' => $values]);

        return view('admin::seo.fieldset', compact('form'))->render();
    }
}
