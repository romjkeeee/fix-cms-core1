<?php

namespace AltSolution\Admin\Http\Controllers;

use AltSolution\Admin\System\Layout;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var Layout
     */
    protected $layout;

    /**
     * @param Layout $layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function boot()
    {
        //
    }

}
