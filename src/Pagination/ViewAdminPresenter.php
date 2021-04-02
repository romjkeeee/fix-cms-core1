<?php

namespace AltSolution\Admin\Pagination;

use Illuminate\Pagination\UrlWindow;
use Illuminate\Pagination\UrlWindowPresenterTrait;
use Illuminate\Contracts\Pagination\Paginator as PaginatorContract;
use Illuminate\Contracts\Pagination\Presenter as PresenterContract;

class ViewAdminPresenter implements PresenterContract
{
    use UrlWindowPresenterTrait;

    /**
     * The paginator implementation.
     *
     * @var \Illuminate\Contracts\Pagination\Paginator
     */
    protected $paginator;

    /**
     * The URL window data structure.
     *
     * @var array
     */
    protected $window;

    /**
     * Create a new Bootstrap presenter instance.
     *
     * @param  \Illuminate\Contracts\Pagination\Paginator  $paginator
     * @param  \Illuminate\Pagination\UrlWindow|null  $window
     */
    public function __construct(PaginatorContract $paginator, UrlWindow $window = null)
    {
        $this->paginator = $paginator;
        $this->window = is_null($window) ? UrlWindow::make($paginator) : $window->get();
    }

    /**
     * Determine if the underlying paginator being presented has pages to show.
     *
     * @return bool
     */
    public function hasPages()
    {
        return $this->paginator->hasPages();
    }

    /**
     * Convert the URL window into Bootstrap HTML.
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function render()
    {
        if ($this->hasPages())
        {
            return view('admin::pagination.presenter', [
                'presenter' => $this,
            ]);
        }

        return '';
    }

    protected function currentPage()
    {
        return $this->paginator->currentPage();
    }

    protected function lastPage()
    {
        return $this->paginator->lastPage();
    }

    public function total()
    {
        return $this->paginator->total();
    }
    
    public function from()
    {
        return ($this->paginator->currentPage()-1) * $this->paginator->perPage() + 1;
    }
    
    public function to()
    {
        return $this->paginator->currentPage() * $this->paginator->perPage();
    }

    public function hasPreviousButton()
    {
        if ($this->paginator->currentPage() <= 1) {
            return false;
        }

        return true;
    }
    
    public function url($page = 1)
    {
        $url = $this->paginator->url($page);

        return $url;
    }
    
    public function relUrl($relativePage = 0)
    {
        $url = $this->paginator->url(
            $this->paginator->currentPage() + $relativePage
        );
        
        return $url;
    }

    public function hasNextButton()
    {
        if (! $this->paginator->hasMorePages()) {
            return false;
        }

        return true;
    }
    
    public function isActive($page = 1)
    {
        return $this->currentPage() == $page;
    }

    public function hasFirstLinks()
    {
        return $this->window['first'] !== null;
    }
    
    public function getFirstLinks()
    {
        return $this->window['first'];
    }

    public function hasSliderLinks()
    {
        return $this->window['slider'] !== null;
    }

    public function getSliderLinks()
    {
        return $this->window['slider'];
    }

    public function hasLastLinks()
    {
        return $this->window['last'] !== null;
    }

    public function getLastLinks()
    {
        return $this->window['last'];
    }

}
