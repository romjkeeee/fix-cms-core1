<?php namespace AltSolution\Admin;

use AltSolution\Admin\System\Layout;
use Illuminate\View\View;

class LayoutComposer
{
    /**
     * @var Layout
     */
    private $layout;

    /**
     * LayoutComposer constructor.
     * @param Layout $layout
     */
    public function __construct(Layout $layout)
    {
        $this->layout = $layout;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('layout', $this->layout->toArray());
    }

}