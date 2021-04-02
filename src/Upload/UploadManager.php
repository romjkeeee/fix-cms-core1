<?php

namespace AltSolution\Admin\Upload;

use Illuminate\Http\UploadedFile;

class UploadManager implements UploadManagerInterface
{
    /**
     * Store uploads in this directory
     * @var string
     */
    protected $directory;
    /**
     * Prefix for generating url
     * @var string
     */
    protected $url;
    /**
     * Permission for creating directories
     * @var int
     */
    protected $permission;

    public function __construct(array $options)
    {
        $this->directory = array_get($options, 'directory');
        if (empty($this->directory)) {
            throw new \Exception('Upload directory must be not empty');
        }
        $this->url = array_get($options, 'url');
        if (empty($this->url)) {
            $this->url = '/' . $this->directory;
        }
        $this->permission = array_get($options, 'permission', 0777);
    }

    function storeUploadedFile(UploadedFile $file, $directory)
    {
        $fileName = $this->newFileName($file->getClientOriginalName());

        $dir = $this->getDir($directory, $fileName);
        if (!is_dir($dir)) {
            mkdir($dir, $this->permission, true);
        }

        $originalPath = $dir . $fileName;
        copy($file->getRealPath(), $originalPath);

        return $fileName;
    }

    function storeFile(\SplFileInfo $file, $directory)
    {
        if (!$file->isFile()) {
            throw new \Exception('Invalid file passed as parameter: ' . $file);
        }
        $fileName = $this->newFileName($file->getFilename());

        $dir = $this->getDir($directory, $fileName);
        if (!is_dir($dir)) {
            mkdir($dir, $this->permission, true);
        }

        $originalPath = $dir . $fileName;
        copy($file->getRealPath(), $originalPath);

        return $fileName;
    }

    function delete($fileName, $directory)
    {
        $dir = $this->getDir($directory, $fileName);
        if (is_file($dir . $fileName)) {
            unlink($dir . $fileName);
        }
    }

    function generateUrl($fileName, $directory)
    {
        if (!$fileName) {
            return null;
        }

        $baseUrl = $this->getUrl($directory, $fileName);
        
        return $baseUrl . $fileName;
    }

    function retrieveFile($fileName, $directory)
    {
        if (!$fileName) {
            return null;
        }

        $dir = $this->getDir($directory, $fileName);
        
        return $dir . $fileName;
    }

    protected function getDir($dir, $fileName)
    {
        $dirSuffix = null;
        if ($fileName) {
            $hash = sha1($fileName);
            $dirSuffix = substr($hash, 0, 2) . '/' . substr($hash, 2, 2) . '/';
        }

        $fullDir = $this->directory . '/' . $dir . '/' . $dirSuffix;
        // TODO: allow absolute directory
        $fullDir = public_path($fullDir);

        return $fullDir;
    }

    protected function getUrl($dir, $fileName)
    {
        $dirSuffix = null;
        if ($fileName) {
            $hash = sha1($fileName);
            $dirSuffix = substr($hash, 0, 2) . '/' . substr($hash, 2, 2) . '/';
        }

        $url = $this->url . '/' . $dir . '/' . $dirSuffix;

        return $url;
    }

    protected function newFileName($oldFileName)
    {
        $ts = microtime(true) * 1e3;
        $tsPart = rtrim(base_convert($ts, 10, 32), '=');
        $fn = sha1($oldFileName);
        $fnPart = substr($fn, rand(0, 32), 8);
        $pathPart = parse_url($oldFileName, PHP_URL_PATH);
        $ext = strtolower(pathinfo($pathPart, PATHINFO_EXTENSION));

        return sprintf('%s_%s.%s', $tsPart, $fnPart, $ext);
    }
}
