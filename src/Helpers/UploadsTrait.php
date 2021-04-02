<?php

namespace AltSolution\Admin\Helpers;

use AltSolution\Admin\Upload\UploadManagerInterface;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * Use on models to add support for uploading files
 * Class UploadsTrait
 * @package AltSolution\Admin\Helpers
 * @implements UploadsInterface
 */
trait UploadsTrait
{
    /**
     * Return array of fields
     * @return array
     */
    public function getUploadsFields()
    {
        return [];
    }

    /**
     * Return relative path where uploads were been stored
     * @return string
     */
    public function getUploadsPath()
    {
        return $this->getTable();
    }

    public function uploadSave($property, UploadedFile $file)
    {
        $manager = app(UploadManagerInterface::class);
        $this[$property] = $manager->storeUploadedFile($file, $this->getUploadsPath());
    }

    public function uploadSaveFile($property, \SplFileInfo $file)
    {
        $manager = app(UploadManagerInterface::class);
        $this[$property] = $manager->storeFile($file, $this->getUploadsPath());
    }

    public function uploadAllSave(Request $request)
    {
        $properties = $this->getUploadsFields();
        foreach ($properties as $property) {
            $file = $request->file($property);
            if ($file) {
                $this->uploadDelete($property);
                $this->uploadSave($property, $file);
            } elseif ($request[$property . '_delete']) {
                $this->uploadDelete($property);
            }
        }
    }

    public function uploadVerbose($property, $local = false)
    {
        if ($local) {
            return $this->uploadFile($property);
        } else {
            return $this->uploadUrl($property);
        }
    }

    public function uploadUrl($property)
    {
        $manager = app(UploadManagerInterface::class);
        return $manager->generateUrl($this[$property], $this->getUploadsPath());
    }

    public function uploadFile($property)
    {
        $manager = app(UploadManagerInterface::class);
        return $manager->retrieveFile($this[$property], $this->getUploadsPath());
    }

    public function uploadDelete($property)
    {
        $manager = app(UploadManagerInterface::class);
        $fileName = $this->getOriginal($property);
        $manager->delete($fileName, $this->getUploadsPath());
        $this[$property] = '';
    }

    public function uploadAllDelete()
    {
        $manager = app(UploadManagerInterface::class);
        foreach ($this->getUploadsFields() as $property) {
            $fileName = $this->getOriginal($property);
            $manager->delete($fileName, $this->getUploadsPath());
            $this[$property] = '';
        }
    }
}
