<?php

namespace AltSolution\Admin\Form\Component;

use AltSolution\Admin\Form\AbstractComponent;

class FormOpen extends AbstractComponent
{
    protected $view = 'form_open';

//    /** @var string */
//    private $action;
//    /** @var string */
//    private $method = 'post';
//    /** @var bool */
//    private $autocomplete = true;
//    /** @var string */
//    private $encoding;
//
//    /**
//     * Setup form action
//     * @param string $action
//     * @return $this
//     */
//    public function setAction($action)
//    {
//        $this->action = $action;
//
//        return $this;
//    }
//
//    /**
//     * Setup form autocomplete
//     * @param bool $value
//     * @return $this
//     */
//    public function setAutocomplete($value)
//    {
//        $this->autocomplete = $value;
//
//        return $this;
//    }
//
//    /**
//     * Setup form method
//     * @param string $method
//     * @return $this
//     * @throws \Exception
//     */
//    public function setMethod($method)
//    {
//        static $methods = ['get', 'post'];
//        $method = strtolower($method);
//        if (!in_array($method, $methods)) {
//            throw new \Exception('Invalid form method');
//        }
//        $this->method = $method;
//
//        return $this;
//    }
//
//    /**
//     * @param string $encoding
//     * @return $this
//     * @throws \Exception
//     */
//    public function setEncoding($encoding)
//    {
//        // enctype
//        
//        static $encodings = [
//            'text/plain',
//            'application/x-www-form-urlencoded',
//            'multipart/form-data',
//        ];
//
//        if (!in_array($encoding, $encodings)) {
//            throw new \Exception('Invalid form encoding');
//        }
//        $this->encoding = $encoding;
//
//        return $this;
//    }
}
