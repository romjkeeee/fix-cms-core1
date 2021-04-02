<?php

namespace AltSolution\Admin\Http\Controllers;

use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Logging\Log;

class CoreController extends Controller
{
    public function getCache()
    {
        $this->authorize('permission', 'cache');

        $this->layout
            ->setActiveSection('options')
            ->setTitle(trans('admin::core.cache_title'));
        return view('admin::core.cache');
    }

    public function postCache()
    {
        $this->authorize('permission', 'cache');

        // core

        /** @var Dispatcher $events */
        $events = app('events');
        /** @var CacheManager $cache */
        $cache = app('cache');

        $storeName = null;
        $events->fire('cache:clearing', [$storeName]);
        $cache->store($storeName)->flush();
        $events->fire('cache:cleared', [$storeName]);

        // twig

        /** @var \Twig_Environment $twig */
        $twig = app('twig');
        /** @var Filesystem $files */
        $files = app('files');

        $cacheDir = $twig->getCache();
        $files->deleteDirectory($cacheDir);


        $log = logger();

        $log->info('Application cache cleared!');

        if ($files->exists($cacheDir)) {
            $log->error('Twig cache failed to be cleaned');
        } else {
            $log->info('Twig cache cleaned');
        }
    }
}
