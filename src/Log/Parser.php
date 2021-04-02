<?php namespace AltSolution\Admin\Log;

class Parser implements ParserInterface
{
    // protected $pattern = '~\[(?P<date>.*)\] (?P<logger>\w+).(?P<level>\w+): (?P<message>.*[^ ]+) (?P<context>[^ ]+) (?P<extra>[^ ]+)~';
    protected $pattern = '~^\[(?P<date>.*)\] (?P<logger>\w+).(?P<level>\w+): (?P<message>.*)~';

    /**
     * @param string|null $pattern
     */
    public function __construct($pattern = null)
    {
        if ($pattern) {
            $this->pattern = $pattern;
        }
    }

    public function parse($str)
    {
        if (!is_string($str)) {
            throw new \InvalidArgumentException('Parameter must be string');
        }

        $found = preg_match($this->pattern, $str, $data);
        // if (!$found) { return null; }

        if ($found) {
            return [
                'type' => 'monolog',
                'content' => [
                    'raw' => $str,
                    //
                    'date' => \DateTime::createFromFormat('Y-m-d H:i:s', $data['date']),
                    'logger' => $data['logger'],
                    'level' => $data['level'],
                    'message' => $data['message'],
                    // 'context' => json_decode($data['context'], true),
                    // 'extra' => json_decode($data['extra'], true)
                ],
            ];
        } else {
            return [
                'type' => 'plain',
                'content' => $str,
            ];
        }
    }
}