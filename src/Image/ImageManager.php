<?php

namespace AltSolution\Admin\Image;

use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Http\UploadedFile;

// TODO: extends upload manager
class ImageManager implements ImageManagerInterface
{
    const FN_NOP = 'nop';
    const FN_COPY = 'copy';
    const FN_CROP = 'crop';
    const FN_RESIZE = 'resize';
    const FN_CROP_HV = 'crop_hv';
    const FN_RESIZE_HV = 'resize_hv';
    const FN_FILL = 'fill';
    /**
     * Vertical - resize, horizontal - crop
     */
    const FN_VRHC = 'vrhc';
    /**
     * Vertcial - fill, horizontal - crop
     */
    const FN_VFHC = 'vfhc';
    /**
     * Vertcial - crop, horizontal - fill
     */
    const FN_VCHF = 'vchf';

    /**
     * Store images in this directory
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
    /**
     * Keep original uploaded image
     * @var bool
     */
    protected $keepSource;
    /**
     * Prevent double encode of image
     * @var bool
     */
    protected $doubleEncode;
    /**
     * Slice images on upload
     * @var bool
     */
    protected $sliceUpload;
    /**
     * Slice images on verbose
     * @var bool
     */
    protected $sliceVerbose;

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
        $this->keepSource = array_get($options, 'keep_source', true);
        $this->doubleEncode = array_get($options, 'double_encode', false);
        $this->sliceUpload = array_get($options, 'slice_upload', true);
        $this->sliceVerbose = array_get($options, 'slice_verbose', false);
        $this->quality = array_get($options, 'quality', 90);
    }

    public function storeUploadedFile(UploadedFile $file, $directory, $sizes = null)
    {
        $fileName = $this->newFileName($file->getClientOriginalName());

        $dir = $this->getDir($directory, $fileName);
        if (!is_dir($dir)) {
            mkdir($dir, $this->permission, true);
        }

        if ($this->keepSource) {
            $originalPath = $dir . $fileName;
            if ($this->doubleEncode) {
                Image::make($file->getRealPath())->save($originalPath, $this->quality);
            } else {
                copy($file->getRealPath(), $originalPath);
            }
        }

        if ($this->sliceUpload && $sizes) {
            foreach ($sizes as $size => $params) {
                $this->storeSize($file->getRealPath(), $dir, $fileName, $size, $params);
            }
        }

        return $fileName;
    }

    public function storeFile(\SplFileInfo $file, $directory, $sizes = null)
    {
        if (!$file->isFile()) {
            throw new \Exception('Invalid file passed as parameter: ' . $file);
        }
        $fileName = $this->newFileName($file->getFilename());

        $dir = $this->getDir($directory, $fileName);
        if (!is_dir($dir)) {
            mkdir($dir, $this->permission, true);
        }

        if ($this->keepSource) {
            $originalPath = $dir . $fileName;
            if ($this->doubleEncode) {
                Image::make($file->getRealPath())->save($originalPath, $this->quality);
            } else {
                copy($file->getRealPath(), $originalPath);
            }
        }

        if ($this->sliceUpload && $sizes) {
            foreach ($sizes as $size => $params) {
                $this->storeSize($file->getRealPath(), $dir, $fileName, $size, $params);
            }
        }

        return $fileName;
    }

    public function storeUrl($url, $directory, $sizes = null)
    {
        $extension = pathinfo($url, PATHINFO_EXTENSION);
        $tempFile = tempnam(sys_get_temp_dir(), 'img') . '.' . $extension;
        $content = file_get_contents($url);
        if ($content === false) {
            throw new \Exception('Failed fetch url: ' . $url);
        }
        file_put_contents($tempFile, $content);
        $file = new \SplFileInfo($tempFile);

        $fileName = $this->storeFile($file, $directory, $sizes);

        @unlink($tempFile);
        //register_shutdown_function('unlink', $tempFile);

        return $fileName;
    }

    public function delete($fileName, $directory, $sizes = null)
    {
        $dir = $this->getDir($directory, $fileName);
        if (is_file($dir . $fileName)) {
            unlink($dir . $fileName);
        }

        if ($sizes) {
            foreach ($sizes as $name => $_) {
                $sizeFileName = sprintf('%s-%s', $name, $fileName);
                if (is_file($dir . $sizeFileName)) {
                    unlink($dir . $sizeFileName);
                }
            }
        }
    }

    public function reset($fileName, $directory, $sizes)
    {
        $dir = $this->getDir($directory, $fileName);

        foreach ($sizes as $name => $_) {
            $sizeFileName = sprintf('%s-%s', $name, $fileName);
            if (is_file($dir . $sizeFileName)) {
                unlink($dir . $sizeFileName);
            }
        }
    }

    public function generateUrl($fileName, $directory, $size = null, array $params = null)
    {
        if (!$fileName) {
            return null;
        }

        $baseUrl = $this->getUrl($directory, $fileName);

        if ($size) {
            $sizeFileName = sprintf('%s-%s', $size, $fileName);

            if ($this->keepSource && $this->sliceVerbose && $params) {
                $localDir = $this->getDir($directory, $fileName);
                $localFile = $localDir . $sizeFileName;
                $sourceFile = $localDir . $fileName;
                if (!file_exists($localFile) && file_exists($sourceFile)) {
                    $this->storeSize($sourceFile, $localDir, $fileName, $size, $params);
                }
            }

            return $baseUrl . $sizeFileName;
        }

        return $baseUrl . $fileName;
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
        // convert float microseconds to int
        $ts = microtime(true) * 1e3;
        $tsPart = rtrim(base_convert($ts, 10, 32), '=');
        $fn = sha1($oldFileName);
        // sha1 length is 40 symbols
        $fnPart = substr($fn, rand(0, 32), 8);
        $pathPart = parse_url($oldFileName, PHP_URL_PATH);
        // change file extension case to lower
        $ext = strtolower(pathinfo($pathPart, PATHINFO_EXTENSION));

        return sprintf('%s_%s.%s', $tsPart, $fnPart, $ext);
    }

    protected function storeSize($sourcePath, $destDir, $fileName, $size, $params)
    {
        $image = Image::make($sourcePath);
        $srcWidth = $image->getWidth();
        $srcHeight = $image->getHeight();
        $isVertical = $srcHeight > $srcWidth;

        list($sizeFunc, $dstWidth, $dstHeight) = $params;

        $exactSize = ($srcWidth == $dstWidth && $srcHeight == $dstHeight);
        if ($exactSize && !$this->doubleEncode) {
            // simply copy file
            $sizeFunc = self::FN_COPY;
        }

        $localSizeFile = $destDir . sprintf('%s-%s', $size, $fileName);
        switch ($sizeFunc) {
            case self::FN_NOP:
                break;

            case self::FN_COPY:
                copy($sourcePath, $localSizeFile);
                return;

            case self::FN_CROP_HV:
                if ($isVertical) {
                    $tmp = $dstWidth;
                    $dstWidth = $dstHeight;
                    $dstHeight = $tmp;
                }
            case self::FN_CROP:
                crop:
                $image->fit($dstWidth, $dstHeight, function ($constraint) {
                    $constraint->upsize();
                });
                break;

            case self::FN_RESIZE_HV:
                if ($isVertical) {
                    $tmp = $dstWidth;
                    $dstWidth = $dstHeight;
                    $dstHeight = $tmp;
                }
            case self::FN_RESIZE:
                resize:
                $image->resize($dstWidth, $dstHeight, function ($constraint) {
                    // save aspect ratio
                    $constraint->aspectRatio();
                    // prevent possible upsizing
                    $constraint->upsize();
                });
                break;

            case self::FN_FILL:
                fill:
                $image->resize($dstWidth, $dstHeight, function ($constraint) {
                    // save aspect ratio
                    $constraint->aspectRatio();
                    // prevent possible upsizing
                    $constraint->upsize();
                });

                $fillColor = array_get($params, 3, '#000000');
                $tmpCanvas = Image::canvas($dstWidth, $dstHeight, $fillColor);
                $tmpCanvas->insert($image, 'center');
                $image = $tmpCanvas;
                break;

            case self::FN_VRHC:
                if ($isVertical) {
                    goto resize;
                }
                else {
                    goto crop;
                }
                break;

            case self::FN_VFHC:
                if ($isVertical) {
                    goto fill;
                }
                else {
                    goto crop;
                }
                break;

            case self::FN_VCHF:
                if ($isVertical) {
                    goto crop;
                }
                else {
                    goto fill;
                }
                break;

            default:
                throw new \Exception('Size function not found: ' . $sizeFunc);
        }
        $image->save($localSizeFile, $this->quality);
    }

}
