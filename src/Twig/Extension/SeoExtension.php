<?php

namespace AltSolution\Admin\Twig\Extension;

use AltSolution\Admin\Helpers\SeoInterface;
use AltSolution\Admin\Seo\SeoManagerInterface;
use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

class SeoExtension extends Twig_Extension
{
    /**
     * @var SeoManagerInterface
     */
    private $seoManager;

    public function __construct(SeoManagerInterface $seoManager)
    {
        $this->seoManager = $seoManager;
    }

    public function getName()
    {
        return 'AltSolution_CMS_SEO';
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('seo_title', [$this, 'seoTitle'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('seo_meta_name', [$this, 'seoMetaName'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('seo_meta_property', [$this, 'seoMetaProperty'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('seo_custom', [$this, 'seoCustom'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('seo_render', [$this, 'seoRender'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('seo_admin_fieldset', [$this, 'seoAdminFieldset'], ['is_safe' => ['html']]),
        ];
    }

    public function seoTitle()
    {
        return $this->seoManager->getTitle();
    }

    public function seoMetaName($name)
    {
        return $this->seoManager->getMetaName($name);
    }

    public function seoMetaProperty($property)
    {
        return $this->seoManager->getMetaProperty($property);
    }

    public function seoCustom($key)
    {
        return $this->seoManager->getCustom($key);
    }

    public function seoRender()
    {
        return $this->seoManager->renderAll();
    }

    public function seoAdminFieldset(SeoInterface $model, $locale)
    {
        return $this->seoManager->renderFieldset($model, $locale);
    }

    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('seo_noindex', [$this, 'seoNoIndex'], ['is_safe' => ['html']]),
        ];
    }

    public function seoNoIndex($value)
    {
        // Yandex bot ignore this text.
        $str = '<noindex>' . $value . '</noindex>';

        return $str;
    }
}
