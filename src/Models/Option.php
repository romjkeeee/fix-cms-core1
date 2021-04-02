<?php

namespace AltSolution\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $table = 'options';
    public $timestamps = false;

    protected $fillable = [
    	'name',
    	'value',
	];
}
