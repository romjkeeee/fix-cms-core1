<?php

namespace AltSolution\Admin\Helpers;

use AltSolution\Admin\Image\ImageManagerInterface;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * Use on models to add support for uploading images
 * Class ImagesTrait
 * @package AltSolution\Admin\Helpers
 * @implements ImagesInterface
 */
trait ImagesTrait
{
    /**
     * Return array of fields that consist of list of image sizes
     * @return array
     */
    abstract public function getImagesFields();

    /**
     * Return relative path where images were been stored
     * @return string
     */
    public function getImagesPath()
    {
        return $this->getTable();
    }

    public function imageSave($property, UploadedFile $file)
    {
        $manager = app(ImageManagerInterface::class);
        $sizes = array_get($this->getImagesFields(), $property);
        $this[$property] = $manager->storeUploadedFile($file, $this->getImagesPath(), $sizes);
    }

    public function imageSaveFile($property, \SplFileInfo $file)
    {
        $manager = app(ImageManagerInterface::class);
        $sizes = array_get($this->getImagesFields(), $property);
        $this[$property] = $manager->storeFile($file, $this->getImagesPath(), $sizes);
    }
    
    public function imageSaveUrl($property, $url)
    {
        $manager = app(ImageManagerInterface::class);
        $sizes = array_get($this->getImagesFields(), $property);
        $this[$property] = $manager->storeUrl($url, $this->getImagesPath(), $sizes);
    }

    public function imageAllSave(Request $request)
    {
        $properties = $this->getImagesFields();
        foreach ($properties as $property => $sizes) {
            $file = $request->file($property);
            if ($file) {
                $this->imageDelete($property);
                $this->imageSave($property, $file);
            } elseif ($request[$property . '_delete']) {
                $this->imageDelete($property);
            }
        }
    }

    public function imageVerbose($property, $size = null)
    {
        return $this->imageUrl($property, $size);
    }

    public function imageUrl($property, $size = null)
    {
        $manager = app(ImageManagerInterface::class);
        if ($size !== null) {
            $sizes = array_get($this->getImagesFields(), $property);
            $params = array_get($sizes, $size);

            return $manager->generateUrl($this[$property], $this->getImagesPath(), $size, $params);
        }

        return $manager->generateUrl($this[$property], $this->getImagesPath());
    }

    public function imageDelete($property)
    {
        $manager = app(ImageManagerInterface::class);
        $fileName = $this->getOriginal($property);
        $sizes = array_get($this->getImagesFields(), $property);
        $manager->delete($fileName, $this->getImagesPath(), $sizes);
        $this[$property] = '';
    }

    public function imageAllDelete()
    {
        $manager = app(ImageManagerInterface::class);
        foreach ($this->getImagesFields() as $property => $sizes) {
            $fileName = $this->getOriginal($property);
            $manager->delete($fileName, $this->getImagesPath(), $sizes);
            $this[$property] = '';
        }
    }

    public function imageReset($property)
    {
        $manager = app(ImageManagerInterface::class);
        $fileName = $this->getOriginal($property);
        $sizes = array_get($this->getImagesFields(), $property);
        $manager->reset($fileName, $this->getImagesPath(), $sizes);
    }

    public function imageAllReset()
    {
        $manager = app(ImageManagerInterface::class);
        foreach ($this->getImagesFields() as $property => $sizes) {
            $fileName = $this->getOriginal($property);
            $manager->reset($fileName, $this->getImagesPath(), $sizes);
        }
    }
}
