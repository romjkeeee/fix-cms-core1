<?php

namespace AltSolution\Admin\Twig\Extension;

use Twig_Extension;
use Twig_SimpleFunction;

class HelpersExtension extends Twig_Extension
{
    public function getName()
    {
        return 'AltSolution_Admin_Helpers';
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('cms_locales', 'cms_locales', ['is_safe' => ['html']]),

            new Twig_SimpleFunction('cms_option', 'cms_option', ['is_safe' => ['html']]),
            new Twig_SimpleFunction('cms_options', 'cms_options', ['is_safe' => ['html']]),

//            new Twig_SimpleFunction('cms_add_css', 'cms_add_css', ['is_safe' => ['html']]),
//            new Twig_SimpleFunction('cms_add_js', 'cms_add_js', ['is_safe' => ['html']]),
//            new Twig_SimpleFunction('cms_render_css', 'cms_render_css', ['is_safe' => ['html']]),
//            new Twig_SimpleFunction('cms_render_js', 'cms_render_js', ['is_safe' => ['html']]),
        ];
    }

}
