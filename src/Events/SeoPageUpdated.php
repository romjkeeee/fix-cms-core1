<?php

namespace AltSolution\Admin\Events;

use Illuminate\Queue\SerializesModels;

class SeoPageUpdated
{
    /**
     * @var string
     */
    public $page;

    public function __construct($page)
    {
        $this->page = $page;
    }
}
