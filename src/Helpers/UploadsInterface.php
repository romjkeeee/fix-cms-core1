<?php

namespace AltSolution\Admin\Helpers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

interface UploadsInterface
{
    /**
     * Save upload in storage
     * @param string $property Upload field
     * @param $file
     * @throws \Exception
     */
    function uploadSave($property, UploadedFile $file);

    /**
     * Save upload in storage by url
     * @param string $property Upload field
     * @param $file
     * @throws \Exception
     */
    function uploadSaveFile($property, \SplFileInfo $file);

    /**
     * Automatic save and delete uploads from storage
     * @param Request $request
     */
    function uploadAllSave(Request $request);

    /**
     * Get upload url
     * @param string $property Upload field
     * @param bool $local Return local or remote url
     * @return null|string
     * @deprecated 1.5.0 Use uploadUrl, uploadFile instead
     */
    function uploadVerbose($property, $local = false);

    /**
     * Get upload url
     * @param string $property Upload field
     * @return null|string
     */
    function uploadUrl($property);

    /**
     * Get upload file
     * @param string $property Upload field
     * @return null|string
     */
    function uploadFile($property);

    /**
     * Delete upload from storage
     * @param string $property Upload field
     */
    function uploadDelete($property);

    /**
     * Delete all uploads from storage
     */
    function uploadAllDelete();
}

