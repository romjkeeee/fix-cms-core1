<?php

namespace AltSolution\Admin\ElFinder;

use elFinderSessionInterface;

class Session implements elFinderSessionInterface
{
    /**
     * @var \Illuminate\Session\SessionInterface
     */
    private $manager;
    /**
     * @var string
     */
    private $prefix;

    public function __construct()
    {
        // TODO: use di
        $this->manager = session();
        $this->prefix = 'elfinder';
    }

    public function start()
    {
        // assume is started by framework middleware
        return $this;
    }

    public function close()
    {
        // assume is write & closed by framework middleware
        return $this;
    }

    public function get($key, $empty = '')
    {
        return $this->manager->get($this->prefix . '.' . $key, $empty);
    }

    public function set($key, $data)
    {
        $this->manager->set($this->prefix . '.' . $key, $data);
        $this->manager->save();

        return $this;
    }

    public function remove($key)
    {
        $this->manager->remove($this->prefix . '.' . $key);
        $this->manager->save();

        return $this;
    }
}