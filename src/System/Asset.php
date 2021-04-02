<?php

namespace AltSolution\Admin\System;

class Asset implements AssetInterface
{
    /**
     * @var array
     */
    private $css = [];
    /**
     * @var array
     */
    private $cssInline = [];
    /**
     * @var array
     */
    private $js = [];
    /**
     * @var array
     */
    private $jsInline = [];

    // todo: default styles and js

    public function addCss($href)
    {
        $hash = md5($href);
        $this->css[$hash] = $href;
    }

    public function addCssInline($css)
    {
        $hash = md5($css);
        $this->cssInline[$hash] = $css;
    }

    public function addJs($src)
    {
        $hash = md5($src);
        $this->js[$hash] = $src;
    }

    public function addJsInline($js)
    {
        $hash = md5($js);
        $this->jsInline[$hash] = $js;
    }

    public function renderCss()
    {
        $result = '';
        foreach ($this->css as $href) {
            //asset()
            $result .= '<link href="' . $href . '" rel="stylesheet" type="text/css" />' . PHP_EOL;
        }
        foreach ($this->cssInline as $css) {
            $result .= '<style type="text/css">' . $css . '</style>' . PHP_EOL;
        }
        return $result;
    }

    public function renderJs()
    {
        $result = '';
        foreach ($this->js as $src) {
            $result .= '<script src="' . $src . '" type="application/javascript"></script>'. PHP_EOL;
        }
        foreach ($this->jsInline as $js) {
            $result .= '<script type="application/javascript">' . $js . '</script>' . PHP_EOL;
        }
        return $result;
    }
}
