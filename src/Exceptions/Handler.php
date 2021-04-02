<?php

namespace AltSolution\Admin\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;

class Handler implements ExceptionHandlerContract
{
    /**
     * @var ExceptionHandlerContract|null
     */
    private $previous;

    public function __construct(ExceptionHandlerContract $previous = null)
    {
        $this->previous = $previous;
    }

    public function report(Exception $e)
    {
        $this->previous === null ?: $this->previous->report($e);
        // TODO: log 403, 404 errors
    }

    public function render($request, Exception $e)
    {
        // TODO: duplicate in System\BootSystem
        $backendUrl = config('admin.admin_url');
        if ($request->is($backendUrl, $backendUrl . '/*')) {
            if ($e instanceof AuthorizationException) {
                return response()->view('admin::errors.403', [], 403);
            }
        }

        return $this->previous === null ? null : $this->previous->render($request, $e);
    }

    public function renderForConsole($output, Exception $e)
    {
        /* @var OutputInterface $output */
        $this->previous === null ?: $this->previous->renderForConsole($output, $e);
    }
}
