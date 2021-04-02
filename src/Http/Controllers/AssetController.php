<?php

namespace AltSolution\Admin\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class AssetController extends BaseController
{
    public function index($path)
    {
        $vendorPrefix = 'vendor/altsolution/cms-core/assets/';
        $realPath = realpath(base_path($vendorPrefix . $path));
        if ($realPath === false) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }
        $content = file_get_contents($realPath);
        $ext = pathinfo($realPath, PATHINFO_EXTENSION);
        $mime = $this->getMime($ext);

        return new Response($content, Response::HTTP_OK, [
            'Content-Type' => $mime,
            'Cache-Control' => 'max-age=' . (2 * 60) . ', public',
            'Etag' => md5($content),
        ]);
    }

    private function getMime($ext)
    {
        switch ($ext) {
            // todo: more mime types
            // https://github.com/cheeaun/kohana-core/blob/master/config/mimes.php
            case 'css': return 'text/css';
            case 'js': return 'application/x-javascript';
            case 'jpg':
            case 'jpeg': return 'image/jpeg';
            case 'png': return 'image/png';
            case 'gif': return 'image/gif';
            case 'html': return 'text/html';
            default: return 'application/octet-stream';
        }
    }
}