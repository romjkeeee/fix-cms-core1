<?php namespace AltSolution\Admin\System;

class PermissionBuilder
{
    /**
     * @var PermissionSection[]
     */
    private $sections = [];
    /**
     * @var PermissionSection
     */
    private $currentSection;

    /**
     * @param string $description
     * @return PermissionSection
     */
    public function addSection($description)
    {
        $section = new PermissionSection($description);
        $this->sections[] = $section;
        $this->currentSection = $section;

        return $section;
    }

    /**
     * @param string $name
     * @param string $description
     * @return PermissionItem
     * @throws \Exception
     */
    public function addPermission($name, $description)
    {
        if ($this->currentSection === null) {
            throw new \Exception('You must add section first');
        }

        $item = new PermissionItem($name, $description);
        $this->currentSection->addItem($item);

        return $item;
    }

    /**
     * @return PermissionSection[]
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * Perform section soring based on it weight
     */
    public function sort()
    {
        usort($this->sections, function(PermissionSection $a, PermissionSection $b) {
            if ($a->getWeight() == $b->getWeight()) {
                return 0;
            }
            return ($a->getWeight() > $b->getWeight()) ? -1 : 1;
        });
    }
}