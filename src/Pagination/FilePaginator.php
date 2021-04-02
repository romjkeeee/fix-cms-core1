<?php namespace AltSolution\Admin\Pagination;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Contracts\Pagination\Presenter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use IteratorAggregate;

class FilePaginator extends AbstractPaginator implements IteratorAggregate, LengthAwarePaginatorContract
{
    /**
     * @var \SplFileObject
     */
    private $file;
    /**
     * The total number of items before slicing.
     *
     * @var int
     */
    protected $total;

    /**
     * The last available page.
     *
     * @var int
     */
    protected $lastPage;

    /**
     * Create a new paginator instance.
     *
     * @param  \SplFileObject $file
     * @param  int  $total
     * @param  int  $perPage
     * @param  int|null  $currentPage
     */
    public function __construct($file, $total, $perPage, $currentPage = null)
    {
        $this->total = $total;
        $this->perPage = $perPage;
        $this->lastPage = (int) ceil($total / $perPage);
        $this->path = $this->resolveCurrentPath();
        $this->currentPage = $this->setCurrentPage($currentPage, $this->lastPage);
        $this->file = $file;
    }

    /**
     * Get the current page for the request.
     *
     * @param  int  $currentPage
     * @param  int  $lastPage
     * @return int
     */
    protected function setCurrentPage($currentPage, $lastPage)
    {
        $currentPage = $currentPage ?: static::resolveCurrentPage();

        return $this->isValidPageNumber($currentPage) ? (int) $currentPage : 1;
    }

    /**
     * Get the URL for the next page.
     *
     * @return string|null
     */
    public function nextPageUrl()
    {
        if ($this->lastPage() > $this->currentPage()) {
            return $this->url($this->currentPage() + 1);
        }
    }

    /**
     * Determine if there are more items in the data source.
     *
     * @return bool
     */
    public function hasMorePages()
    {
        return $this->currentPage() < $this->lastPage();
    }

    /**
     * Get the total number of items being paginated.
     *
     * @return int
     */
    public function total()
    {
        return $this->total;
    }

    /**
     * Get the last page.
     *
     * @return int
     */
    public function lastPage()
    {
        return $this->lastPage;
    }

    /**
     * Render the paginator using the given presenter.
     *
     * @param  \Illuminate\Contracts\Pagination\Presenter|null  $presenter
     * @return string
     */
    public function links(Presenter $presenter = null)
    {
        return $this->render($presenter);
    }

    /**
     * Render the paginator using the given presenter.
     *
     * @param  \Illuminate\Contracts\Pagination\Presenter|null  $presenter
     * @return string
     */
    public function render(Presenter $presenter = null)
    {
        if (is_null($presenter) && static::$presenterResolver) {
            $presenter = call_user_func(static::$presenterResolver, $this);
        }

        $presenter = $presenter ?: new ViewAdminPresenter($this);

        return $presenter->render();
    }

    public function getIterator()
    {
        $this->file->seek($this->perPage * ($this->currentPage-1));
        $limit = $this->perPage;
        while ($this->file->valid() && $limit--) {
            yield $this->file->current();
            $this->file->next();

        }

        // return new \LimitIterator(new \NoRewindIterator($this->file), 0, $this->perPage);
    }

}
