<?php

namespace AltSolution\Admin\System;

interface AssetInterface
{
    /**
     * @param string $href
     */
    public function addCss($href);

    /**
     * @param string $css
     */
    public function addCssInline($css);

    /**
     * @param string $src
     */
    public function addJs($src);

    /**
     * @param string $js
     */
    public function addJsInline($js);

    /**
     * @return string
     */
    public function renderCss();

    /**
     * @return string
     */
    public function renderJs();
}
