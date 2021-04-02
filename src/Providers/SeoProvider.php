<?php

namespace AltSolution\Admin\Providers;

use AltSolution\Admin\Helpers\SeoInterface;
use AltSolution\Admin\Seo\SeoManager;
use AltSolution\Admin\Seo\SeoManagerInterface;
use AltSolution\Admin\Seo\SeoRepository;
use AltSolution\Admin\Seo\SeoRepositoryInterface;
use AltSolution\Admin\Twig;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;

class SeoProvider extends ServiceProvider
{
    public function register()
    {
        //$this->app->bind('seo', SeoManagerInterface::class);
        $this->app->singleton(SeoManagerInterface::class, SeoManager::class);
        $this->app->singleton(SeoRepositoryInterface::class, SeoRepository::class);

        $this->mergeConfigFrom(__DIR__ .'/../../config/seo.php', 'seo');

        // twig form extension
        $this->app['config']->prepend('twigbridge.extensions.enabled', Twig\Extension\SeoExtension::class);
    }

    public function boot()
    {
//        $this->listenEvents();
    }

//    private function listenEvents()
//    {
//        /** @var Dispatcher $events */
//        $events = $this->app['events'];
//
//        $events->listen('eloquent.saved*', function ($model) {
//            if ($model instanceof SeoInterface) {
//                $model->seoOnSaved();
//            }
//        });
//        $events->listen('eloquent.deleted*', function ($model) {
//            if ($model instanceof SeoInterface) {
//                $model->seoOnDeleted();
//            }
//        });
//    }
}
