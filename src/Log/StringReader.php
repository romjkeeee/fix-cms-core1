<?php namespace AltSolution\Admin\Log;

class StringReader implements ReaderInterface
{
    /**
     * @var string
     */
    private $str;

    /**
     * StringReader constructor.
     * @param string $str
     */
    public function __construct($str)
    {
        $this->str = $str;
    }

}