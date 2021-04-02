<?php namespace AltSolution\Admin\System;

use Illuminate\Contracts\Auth\Access\Gate;

class MenuBuilder
{
    /**
     * @var MenuSection[]
     */
    private $sections = [];
    /**
     * @var MenuSection
     */
    private $currentSection;

    /**
     * @param string $name
     * @param string $icon
     * @param string $link
     * @return MenuSection
     */
    public function addSection($name, $icon, $link = null)
    {
        $section = new MenuSection($name, $icon, $link);
        $this->sections[] = $section;
        $this->currentSection = $section;

        return $section;
    }

    /**
     * @param string $alias
     * @return MenuSection
     * @throws Exception
     */
    public function getSection($alias)
    {
        $currentSection = null;
        foreach ($this->sections as $section) {
            if ($section->getAlias() == $alias) {
                $currentSection = $section;
                break;
            }
        }
        if ($currentSection === null) {
            throw new Exception('Section not defined: ' . $alias);
        }
        $this->currentSection = $currentSection;

        return $currentSection;
    }
    
    /**
     * @param string $name
     * @param string $link
     * @return MenuItem
     * @throws \Exception
     */
    public function addItem($name, $link)
    {
        if ($this->currentSection === null) {
            throw new \Exception('You must add section first');
        }

        $item = new MenuItem($name, $link);
        $this->currentSection->addItem($item);

        return $item;
    }

    /**
     * @return MenuSection[]
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * Check menu for permissions
     */
    public function checkPermission()
    {
        /** @var Gate $gate */
        $gate = app(Gate::class);
        
        $sectionToDelete = [];
        
        foreach ($this->getSections() as $sectionIndex=>$section)
        {
            $itemsToDelete = [];
            
            foreach ($section->getItems() as $itemIndex=>$item)
            {
                $hasPerm = true;
                $requiredPermission = $item->getPermission();
                if ($requiredPermission) {
                    $hasPerm = $gate->check('permission', $requiredPermission);
                }
                
                if (!$hasPerm) {
                    $itemsToDelete[] = $itemIndex;
                }
            }
            
            $section->deleteItems($itemsToDelete);
            
            $keepSection = $section->hasItems() || $section->getLink();
            if ($keepSection)
            {
                $requiredPermission = $section->getPermission();
                if ($requiredPermission) {
                    $keepSection = $gate->check('permission', $requiredPermission);
                }
            }
            
            if (!$keepSection) {
                $sectionToDelete[] = $sectionIndex;
            }
        }
        
        $this->deleteSections($sectionToDelete);
    }

    /**
     * Perform section soring based on it weight
     */
    public function sort()
    {
        usort($this->sections, function(MenuSection $a, MenuSection $b) {
            if ($a->getWeight() == $b->getWeight()) {
                return 0;
            }
            return ($a->getWeight() > $b->getWeight()) ? -1 : 1;
        });
    }

    /**
     * Delete sections by index
     * @param array $toDelete
     */
    private function deleteSections(array $toDelete)
    {
        foreach ($toDelete as $index) {
            unset($this->sections[$index]);
        }
    }
}