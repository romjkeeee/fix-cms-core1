<?php namespace AltSolution\Admin\System;

use Illuminate\Contracts\Support\Arrayable;

class Layout implements Arrayable
{
    /**
     * @var string
     */
    private $title;
    /**
     * @var User
     */
    private $user;
    /**
     * @var Menu
     */
    private $menu;
    /**
     * @var Breadcrumbs
     */
    private $breadcrumbs;
    /**
     * @var Notify
     */
    private $notify;

    /**
     * Layout constructor.
     * @param User $user
     * @param Menu $menu
     * @param Breadcrumbs $breadcrumbs
     * @param Notify $notify
     */
    public function __construct(User $user, Menu $menu, Breadcrumbs $breadcrumbs, Notify $notify)
    {
        $this->user = $user;
        $this->menu = $menu;
        $this->breadcrumbs = $breadcrumbs;
        $this->notify = $notify;
    }

    /**
     * @param string $title
     * @return Layout
     */
    public function setTitle($title)
    {
        $this->title = $title;
        
        return $this;
    }
    
    public function toArray()
    {
        return [
            'title' => $this->title,
            'user' => $this->user,
            'menu' => $this->menu,
            'breadcrumbs' => $this->breadcrumbs,
            'notify' => $this->notify,
        ];
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @return Breadcrumbs
     */
    public function getBreadcrumbs()
    {
        return $this->breadcrumbs;
    }

    /**
     * Set current menu section
     * @param string $alias
     * @return $this
     * @throws \Exception
     */
    public function setActiveSection($alias)
    {
        $section = $this->menu->getSection($alias);
        $section->setActive(true);
        
        return $this;
    }

    /**
     * @param string $description
     * @param string|null $url
     * @return $this
     */
    public function addBreadcrumb($description, $url = null)
    {
        $item = new BreadcrumbsItem($description, $url);
        $this->breadcrumbs->addItem($item);

        return $this;
    }

    /**
     * @param string $type
     * @param string $message
     * return $this
     */
    public function addNotify($type, $message)
    {
        $this->notify->add($type, $message);
    }

}