<?php

namespace AltSolution\Admin\Image;

use Illuminate\Http\UploadedFile;

interface ImageManagerInterface
{
    function storeUploadedFile(UploadedFile $file, $directory, $sizes = null);

    function storeFile(\SplFileInfo $file, $directory, $sizes = null);
    
    function storeUrl($url, $directory, $sizes = null);

    function delete($fileName, $directory, $sizes = null);

    function generateUrl($fileName, $directory, $size = null, array $params = null);

    // TODO: retrieveFile()
}
