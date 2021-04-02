<?php namespace AltSolution\Admin\System;

class User
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $avatar;

    /**
     * User constructor.
     * @param integer $id
     * @param string $avatar
     */
    public function __construct($id, $avatar)
    {
        $this->id = $id;
        $this->avatar = $avatar;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }
}