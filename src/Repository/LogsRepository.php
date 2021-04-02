<?php

namespace AltSolution\Admin\Repository;

use AltSolution\Admin\Log\LogFileObject;
use AltSolution\Admin\Log\Parser;

class LogsRepository
{
    /**
     * @return \SplFileInfo[]
     */
    private function getFiles()
    {
        $dirs = config('admin.logs_dirs');
        $extensions = config('admin.logs_extensions');

        $files = [];

        foreach ($dirs as $dir) {
            $dirIterator = new \DirectoryIterator($dir);
            foreach ($dirIterator as $item) {
                if ($item->isDot()) {
                    continue;
                }

                if ($item->isFile()) {
                    $extension = $item->getExtension();
                    if (in_array($extension, $extensions)) {
                        $files[$item->getFilename()] = $item->getFileInfo();
                    }
                }
            }
        }

        return $files;
    }

    public function getLogs()
    {
        $linesPreview = config('admin.logs_lines_preview');

        $logs = [];

        $files = $this->getFiles();
        foreach ($files as $file) {
            $path = $file->getRealPath();
            $logs[] = [
                'path' => $path,
                'name' => $file->getFilename(),
                'created' => $file->getCTime(),
                'modified' => $file->getMTime(),
                'hasMore' => $this->countLines($path) > $linesPreview,
                'content' => $this->tailLog($path, $linesPreview),
            ];
        }

        usort($logs, function ($a, $b) {
            if ($a['created'] == $b['created']) {
                // todo: support unicode
                return strcmp($a['name'], $b['name']) * -1;
            }
            return ($a['created'] > $b['created']) ? -1 : 1;
        });

        return $logs;
    }

    /**
     * todo: make more OOP
     * @param $path
     * @param $lines
     * @return array
     */
    private function tailLog($path, $lines)
    {
        $str = $this->tail($path, $lines);
        $str_lines = explode("\n", $str);

        $parser = new Parser();

        $records = [];
        foreach ($str_lines as $line) {
            /*
            $last = null;
            if (count($records)) {
                $last = &$records[count($records)-1];
            }
                if ($last && $last['type'] == 'plain') {
                    $last['content'][] = $line;
                } else {
                }
            unset($last);
            */

            $records[] = $parser->parse($line);

        }

        return $records;
    }

    public function getLog($file)
    {
        $files = $this->getFiles();

        if (!array_key_exists($file, $files)) {
            return false;
        }

        $file = $files[$file];
        $path = $file->getRealPath();
        $fileObject = new LogFileObject($path, 'r');
        $fileObject->setParser(new Parser());

        return [
            'path' => $path,
            'name' => $file->getFilename(),
            'modified' => $file->getMTime(),
            'lines' => $this->countLines($path),
            'content' => $fileObject,
        ];

    }

    private function readFile($path, $offset = 0, $limit = 100)
    {
        /*
        $f = fopen($path, 'r');

        $numLines = 100;
        $maxLineLength = 200;

        fseek($f, -($numLines * $maxLineLength), SEEK_END);

        $lines = array();
        while (!feof($f)) {
            $lines[] = fgets($f);
        }
        */

        $file = new \SplFileObject($path, 'r');
        $file->seek($offset);
        $lines = new \LimitIterator($file, 0, $limit);
        $topLines = iterator_to_array($lines);

        return $topLines;
    }

    private function countLines($path)
    {
        $f = fopen($path, 'rb');
        $lines = 0;

        while (!feof($f)) {
            $lines += substr_count(fread($f, 8192), "\n");
        }

        fclose($f);

        return $lines;
    }

    private function tail($path, $lines = 100, $adaptive = true)
    {
        $f = fopen($path, "rb");
        if ($f === false) {
            return false;
        }

        // Sets buffer size
        if (!$adaptive) {
            $buffer = 4096;
        } else {
            $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));
        }

        // Jump to last character
        fseek($f, -1, SEEK_END);

        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($f, 1) != "\n") {
            $lines -= 1;
        }

        // Start reading
        $output = '';
        // While we would like more
        while (ftell($f) > 0 && $lines >= 0) {
            // Figure out how far back we should jump
            $seek = min(ftell($f), $buffer);
            // Do the jump (backwards, relative to where we are)
            fseek($f, -$seek, SEEK_CUR);
            // Read a chunk and prepend it to our output
            $output = ($chunk = fread($f, $seek)) . $output;
            // Jump back to where we started reading
            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
            // Decrease our line counter
            $lines -= substr_count($chunk, "\n");
        }
        // While we have too many lines
        // (Because of buffer size we might have read too many)
        while ($lines++ < 0) {
            // Find first newline and remove all text before that
            $output = substr($output, strpos($output, "\n") + 1);
        }
        // Close file and return
        fclose($f);

        return trim($output);
    }

    private function parseLog()
    {
        // $pattern = "/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*/";
    }
}