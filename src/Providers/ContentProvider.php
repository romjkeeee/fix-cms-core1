<?php

namespace AltSolution\Admin\Providers;

use AltSolution\Admin\Forms\ContentForm;
use AltSolution\Admin\Http\Controllers\ContentController;
use AltSolution\Admin\Models\Content;
use AltSolution\Admin\Modules\Content\ContentFormInterface;
use AltSolution\Admin\Modules\Content\ContentModelInterface;
use Illuminate\Support\ServiceProvider;

class ContentProvider extends ServiceProvider
{
    protected $defer = true;

    public function provides()
    {
        return [
            ContentFormInterface::class,
            ContentModelInterface::class,
            'admin.controller.content',
        ];
    }

    public function register()
    {
        $this->app->bind(ContentFormInterface::class, ContentForm::class);
        $this->app->bind(ContentModelInterface::class, Content::class);
        $this->app->instance('admin.controller.content', ContentController::class);
    }
}
