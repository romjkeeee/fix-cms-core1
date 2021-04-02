<?php

namespace AltSolution\Admin\Models;

use AltSolution\Admin\Helpers\TranslateInterface;
use AltSolution\Admin\Helpers\TranslateTrait;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model implements TranslateInterface
{
    use TranslateTrait;

    // TODO: @new_version rename table
    protected $table = 'temails';
    
    protected $fillable = [
    	'name',
    	'desc',
    	'from',
    	'to',

    	'subject',
    	'body',

        'subject_*',
        'body_*',
    ];

    // TODO: @new_version use timestamps
    public $timestamps = false;
}
