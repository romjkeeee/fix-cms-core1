<?php namespace AltSolution\Admin\System;

class MenuItem
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $link;
    /**
     * @var string
     */
    private $permission;

    /**
     * MenuItem constructor.
     * @param string $name
     * @param string $link
     */
    public function __construct($name, $link)
    {
        $this->name = $name;
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Used for selecting admin startup page
     * @return string
     */
    public function getRelativeLink()
    {
        return parse_url($this->link, PHP_URL_PATH);
    }

    public function isActive()
    {
        /*
        $request = app('request');
        $request_path = $request->getPathInfo();
        $link_path = parse_url($this->link, PHP_URL_PATH);

        if (stripos($request_path, $link_path) !== false) {
            return true;
        }
        */
        return false;
   }

    /**
     * @return string
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @param string $permission
     * @return MenuItem
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;
        return $this;
    }

}