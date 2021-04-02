<?php

namespace AltSolution\Admin\Events;

use AltSolution\Admin\Helpers\SeoInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

class SeoModelDeleted
{
    /**
     * @var SeoInterface|Model
     */
    public $model;

    public function __construct(SeoInterface $model)
    {
        $this->model = $model;
    }
}
