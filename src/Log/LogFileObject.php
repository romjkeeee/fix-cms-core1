<?php namespace AltSolution\Admin\Log;

class LogFileObject extends \SplFileObject
{

    /**
     * @var ParserInterface
     */
    private $parser;

    public function setParser(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    public function current()
    {
        $value = parent::current();
        $record = $this->parser->parse($value);
        
        return $record;
    }

}