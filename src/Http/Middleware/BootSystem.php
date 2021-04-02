<?php

namespace AltSolution\Admin\Http\Middleware;

use AltSolution\Admin\System\System;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class BootSystem
{
    /**
     * @var System
     */
    private $system;

    /**
     * BootSystem constructor.
     * @param System $system
     */
    public function __construct(System $system)
    {
        $this->system = $system;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $this->bootSystem($request);

        return $next($request);
    }

    private function bootSystem(Request $request)
    {
        // TODO: duplicate in Exceptions\Handler
        $backendUrl = config('admin.admin_url');
        if ($request->is($backendUrl, $backendUrl . '/*')) {
            $this->system->setIsBackend();

            $modules = app()->tagged('admin.system.module');
            $this->system->registerModules($modules);
            $this->system->bootModules();
        }
    }
}
