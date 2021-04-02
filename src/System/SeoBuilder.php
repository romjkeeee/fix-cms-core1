<?php
namespace AltSolution\Admin\System;

class SeoBuilder
{
    /**
     * @var SeoSection[]
     */
    private $sections = [];
    /**
     * @var SeoSection
     */
    private $currentSection;

    public function addSection($name, $description)
    {
        $section = new SeoSection($name, $description);
        $this->sections[] = $section;
        $this->currentSection = $section;

        return $section;
    }

    public function addField($name, $description, $help = null)
    {
        if ($this->currentSection === null) {
            throw new \Exception('You must add section first');
        }

        $item = new SeoField($name, $description, $help);
        $this->currentSection->addField($item);

        return $item;
    }

    public function addDefaultFields($help = null)
    {
        if ($this->currentSection === null) {
            throw new \Exception('You must add section first');
        }

        $fields = config('seo.default_fields');
        foreach ($fields as $name => $options) {
            $item = new SeoField($name, array_get($options, 'label'), $help);
            $this->currentSection->addField($item);
        }
    }

    public function getSections()
    {
        return $this->sections;
    }

    public function sort()
    {
        usort($this->sections, function(SeoSection $a, SeoSection $b) {
            if ($a->getWeight() == $b->getWeight()) {
                return 0;
            }
            return ($a->getWeight() > $b->getWeight()) ? -1 : 1;
        });
    }
}