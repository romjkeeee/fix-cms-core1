<?php

namespace AltSolution\Admin\Http\Controllers;

class IndexController extends Controller
{
    public function index()
    {
        $startPage = cms_option('admin_start_page');
        if ($startPage) {
            return redirect($startPage);
        }

        return redirect()->route('admin::user_list');
    }
}
