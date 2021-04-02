<?php

namespace AltSolution\Admin\Seo;

class Sitemap
{
    const DEFAULT_PROTOCOL = 'http://www.sitemaps.org/schemas/sitemap/0.9';
    const MAX_LINKS = 50000;
    const DEFAULT_LAST_MOD = 'now';
    const DEFAULT_PRIORITY = '0.5';
    const DEFAULT_CHANGE_FREQ = 'daily';

    protected $items = [];

    private function totalIndexes()
    {
        $total = count($this->items);
        $indexes = round($total / self::MAX_LINKS);

        return max($indexes, 1);
    }

    public function addItem($location, $lastmod = null, $priority = null, $changeFreq = null)
    {
        $lastmod = $lastmod != null ? $lastmod : self::DEFAULT_LAST_MOD;
        $priority = $priority != null ? $priority : self::DEFAULT_PRIORITY;
        $changeFreq = $changeFreq != null ? $changeFreq : self::DEFAULT_CHANGE_FREQ;
        $date = new \DateTime();
        $lastmod = $date->modify($lastmod)->getTimestamp();

        $this->items[] = [
            'loc' => $location,
            'lastmod' => $lastmod,
            'priority' => $priority,
            'changefreq' => $changeFreq
        ];
    }

    public function render()
    {
        $doc = new \DomDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;

        $root = $doc->createElement('urlset');
        $attr = $doc->createAttribute('xmlns');
        $attr->value = self::DEFAULT_PROTOCOL;
        $root->appendChild($attr);
        $root = $doc->appendChild($root);

        foreach ($this->items as $item) {
            $occ = $doc->createElement('url');
            $occ = $root->appendChild($occ);

            $child = $doc->createElement('loc');
            $child = $occ->appendChild($child);
            $value = $doc->createTextNode($this->url . "" . $item['loc']);
            $child->appendChild($value);

            $child = $doc->createElement('lastmod');
            $child = $occ->appendChild($child);
            $value = $doc->createTextNode(date("Y-m-d", $item['lastmod']));
            $child->appendChild($value);

            $child = $doc->createElement('priority');
            $child = $occ->appendChild($child);
            $value = $doc->createTextNode($item['priority']);
            $child->appendChild($value);

            $child = $doc->createElement('changefreq');
            $child = $occ->appendChild($child);
            $value = $doc->createTextNode($item['changefreq']);
            $child->appendChild($value);
        }

        return $doc->saveXML();
    }

    public function renderIndex()
    {
        $doc = new \DomDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;

        $sitemapindex = $doc->createElement('sitemapindex');
        $xmlns = $doc->createAttribute('xmlns');
        $xmlns->value = self::DEFAULT_PROTOCOL;
        $sitemapindex->appendChild($xmlns);
        $sitemapindex = $doc->appendChild($sitemapindex);

        $sitemap = $doc->createElement('sitemap');
        $sitemap = $sitemapindex->appendChild($sitemap);

        $loc = $doc->createElement('loc');
        $loc = $sitemap->appendChild($loc);
        $locVal = $doc->createTextNode($this->path);
        $loc->appendChild($locVal);

        $lm = $doc->createElement('lastmod');
        $lm = $sitemap->appendChild($lm);
        $lmVal = $doc->createTextNode((new \DateTime())->format(DATE_W3C));
        $lm->appendChild($lmVal);

        return $doc->saveXML();
    }
}
