<?php

namespace AltSolution\Admin\Http\Controllers;

use AltSolution\Admin\ElFinder\Session;
use Carbon\Carbon;

class ElFinderController extends Controller
{
    public function index()
    {
        return view('admin::elfinder.index');
    }

    public function connect()
    {
        $rootsList = config('admin.elfinder_roots');
        //$useTrash = config('admin.elfinder_trash');

        $rootDefaults = [
            'driver' => 'LocalFileSystem',
            // All Mimetypes not allowed to upload
            'uploadDeny' => ['all'],
            // Mimetype `image` and `text/plain` allowed to upload
            'uploadAllow' => ['image', 'text/plain'],
            // allowed Mimetype `image` and `text/plain` only
            'uploadOrder' => ['deny', 'allow'],
            // disable and hide dot starting files (OPTIONAL)'
            'accessControl' => [$this, 'rwAccess'],
        ];

        $roots = [];
        foreach ($rootsList as $alias => $root) {
            if (is_array($root)) {
                if (empty($root['alias']) && !is_numeric($alias)) {
                    $root['alias'] = $alias;
                }
            } else {
                $root = [
                    'path' => public_path($root),
                    //'startPath' => public_path($root),
                    'URL' => url($root),
                    'alias' => is_numeric($alias) ? null : $alias,
                ];
            }

            $root = $this->replaceVariables($root);

            if (empty($root['path'])) {
                continue;
            }

            // auto create paths
            foreach (['path', 'startPath'] as $pathKey) {
                if (!empty($root[$pathKey])) {
                    if (!is_dir($root[$pathKey])) {
                        mkdir($root[$pathKey], 0777, true);
                    }
                }
            }

            $roots[] = array_merge($rootDefaults, $root);
        }

        // Documentation for connector options:
        // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options
        $session = new Session();
        $options = [
            //'debug' => true,
            'session' => $session,
            'roots' => $roots,
            // 'uploadTempPath' => '',
            // 'commonTempPath' => '',
        ];

        // run elFinder
        $elFinder = new \elFinder($options);
        $connector = new \elFinderConnector($elFinder);
        $connector->run();

        // TODO: https://github.com/barryvdh/laravel-elfinder/blob/master/src/Connector.php
    }

    private function replaceVariables(array $root)
    {
        // todo: extract to separated class
        $varCallback = function ($match) {
            $var = $match[1];
            list($var, $params) = array_pad(explode(':', $var), 2, null);
            if ($var == 'user_id') {
                return auth()->id();
            } elseif ($var == 'date') {
                $d = Carbon::now();
                return $params ? $d->format($params) : $d->format('Y_m_d');
            }
            return null;
        };
        foreach (['path', 'startPath', 'URL'] as $key) {
            if (isset($root[$key])) {
                $root[$key] = preg_replace_callback('~%(.+?)%~i', $varCallback, $root[$key]);
            }
        }

        if (isset($root['alias'])) {
            $root['alias'] = trans($root['alias']);
        }

        return $root;
    }

    public function rwAccess($attr, $path, $data, $volume, $isDir, $relpath)
    {
        $basename = basename($path);
        return $basename[0] === '.' && strlen($relpath) !== 1 // if file/folder begins with '.' (dot) but with out volume root
            ? !($attr == 'read' || $attr == 'write') // set read+write to false, other (locked+hidden) set to true
            : null; // else elFinder decide it itself
    }

    public function roAccess($attr, $path, $data, $volume, $isDir, $relpath)
    {
        $basename = basename($path);
        return $basename[0] === '.' && strlen($relpath) !== 1 // if file/folder begins with '.' (dot) but with out volume root
            ? !($attr == 'read' || $attr == 'write') // set read+write to false, other (locked+hidden) set to true
            : ($attr == 'read' || $attr == 'locked'); // else read only
    }
}