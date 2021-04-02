<?php

namespace AltSolution\Admin;

use Illuminate\Support\Facades\Facade;

class SeoFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'altsolution.seo';
    }
}