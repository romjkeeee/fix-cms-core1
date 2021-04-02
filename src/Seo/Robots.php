<?php

namespace AltSolution\Admin\Seo;

class Robots
{
    const DEFAULT_AGENTS = "*";
    const DEFAULT_DISALLOW = "*";
    const DEFAULT_NO_INDEX = "";

    private $sitemap = [];
    private $agents = [];
    private $disallow = [];
    private $noIndex = [];

    public function addUserAgent($agent = null)
    {
        $agent = $agent == null ? self::DEFAULT_AGENTS : $agent;
        $this->agents[] = $agent;
    }

    public function addDisallow($path = null)
    {
        $path = $path == null ? self::DEFAULT_DISALLOW : $path;
        $this->disallow[] = $path;
    }

    public function addSitemap($sitemap = null)
    {
        $sitemap = $sitemap == null ? false : $sitemap;
        $this->sitemap[] = $sitemap;
    }

    public function addNoIndex($noIndex = null)
    {
        $noIndex = $noIndex == null ? self::DEFAULT_NO_INDEX : $noIndex;
        $this->noIndex[] = $noIndex;
    }

    public function render()
    {
        //        Sitemap: http://www.example.com/sitemap.xml
        //        User-agent: *
        //        Disallow: *

        $robots = '';
        foreach ($this->sitemap as $sitemap) {
            $robots .= "Sitemap: $sitemap\n";
        }
        foreach ($this->agents as $agent) {
            $robots .= "User-agent: $agent\n";
        }
        foreach ($this->disallow as $disallow) {
            $robots .= "Disallow: $disallow \n";
        }
        foreach ($this->noIndex as $noIndex) {
            $robots .= "Noindex: $noIndex \n";
        }

        return $robots;
    }
}
