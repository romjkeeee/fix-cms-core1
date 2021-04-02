<?php

namespace AltSolution\Admin\Http\Controllers;

use AltSolution\Admin\Pagination\FilePaginator;
use AltSolution\Admin\Repository\LogsRepository;

class LogsController extends Controller
{
    /**
     * @var LogsRepository
     */
    private $logsRepo;

    /**
     * LogsController constructor.
     * @param LogsRepository $logsRepo
     */
    public function __construct(LogsRepository $logsRepo)
    {
        $this->logsRepo = $logsRepo;
    }

    public function index()
    {
        $this->authorize('permission', 'logs');

        $logs = $this->logsRepo->getLogs();

        $this->layout
            ->setActiveSection('options')
            ->setTitle(trans('admin::log.title'));
        return view('admin::logs.list', compact('logs'));
    }

    public function view($file)
    {
        $this->authorize('permission', 'logs');

        $log = $this->logsRepo->getLog($file);

        $perPage = config('admin.logs_lines_per_page');
        $paginator = new FilePaginator($log['content'], $log['lines'], $perPage);

        $this->layout
            ->setActiveSection('options')
            ->setTitle($file)
            ->addBreadcrumb(trans('admin::log.title'), route('admin::logs_list'));
        return view('admin::logs.view', compact('file', 'log', 'paginator'));
    }
}
