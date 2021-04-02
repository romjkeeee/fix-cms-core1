<?php

namespace AltSolution\Admin\Upload;

use Illuminate\Http\UploadedFile;

interface UploadManagerInterface
{
    function storeUploadedFile(UploadedFile $file, $directory);

    function storeFile(\SplFileInfo $file, $directory);

    function delete($fileName, $directory);

    function generateUrl($fileName, $directory);

    function retrieveFile($fileName, $directory);
}
