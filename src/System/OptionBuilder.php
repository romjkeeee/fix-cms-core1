<?php

namespace AltSolution\Admin\System;

use AltSolution\Admin\Form\BuilderInterface;

class OptionBuilder
{
    /**
     * @var OptionSection[]
     */
    private $sections = [];
    /**
     * @var OptionSection
     */
    private $currentSection;

    public function addSection($name, $description)
    {
        $formBuilder = app(BuilderInterface::class);
        $section = new OptionSection($formBuilder, $name, $description);
        $this->sections[] = $section;
        $this->currentSection = $section;

        return $section;
    }

    /**
     * @param string $name
     * @return OptionSection
     * @throws Exception
     */
    public function getSection($name)
    {
        $currentSection = null;
        foreach ($this->sections as $section) {
            if ($section->getName() == $name) {
                $currentSection = $section;
                break;
            }
        }
        if ($currentSection === null) {
            throw new Exception('Section not defined: ' . $name);
        }
        $this->currentSection = $currentSection;

        return $currentSection;
    }

    public function addField($name, array $options = [])
    {
        if ($this->currentSection === null) {
            throw new \Exception('You must add section first');
        }

        $this->currentSection
            ->getFormBuilder()
            // todo: add many. really?
            ->addMany([$name => $options]);
    }

    public function getSections()
    {
        return $this->sections;
    }

    public function sort()
    {
        usort($this->sections, function(OptionSection $a, OptionSection $b) {
            if ($a->getWeight() == $b->getWeight()) {
                return 0;
            }
            return ($a->getWeight() > $b->getWeight()) ? -1 : 1;
        });
    }
}