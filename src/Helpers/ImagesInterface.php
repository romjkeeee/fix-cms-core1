<?php

namespace AltSolution\Admin\Helpers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

interface ImagesInterface
{
    /**
     * Save image in storage
     * @param string $property Image field
     * @param $file
     * @throws \Exception
     */
    function imageSave($property, UploadedFile $file);

    /**
     * Save image in storage by url
     * @param string $property Image field
     * @param $file
     * @throws \Exception
     */
    function imageSaveFile($property, \SplFileInfo $file);

    /**
     * Automatic save and delete images from storage
     * @param Request $request
     */
    function imageAllSave(Request $request);

    /**
     * Get image url for specified size
     * @param string $property Image field
     * @param string $size Image size
     * @return null|string
     * @deprecated 1.5.0 Use imageUrl instead
     */
    function imageVerbose($property, $size = null);

    /**
     * Get image url for specified size
     * @param string $property Image field
     * @param string $size Image size
     * @return null|string
     */
    function imageUrl($property, $size = null);

    // TODO: imageFile()

    /**
     * Delete image from storage
     * @param string $property Image field
     */
    function imageDelete($property);

    /**
     * Delete all images from storage
     */
    function imageAllDelete();

    /**
     * Delete image slicing from storage
     * @param string $property Image field
     */
    function imageReset($property);

    /**
     * Delete all images slicing from storage
     */
    function imageAllReset();
}

