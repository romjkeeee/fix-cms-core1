<?php

namespace AltSolution\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];
    public $timestamps = false;

    public function items()
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort');
    }

    public function parentItems()
    {
        return $this->hasMany(MenuItem::class)->where('parent_id', 0)->orderBy('sort');
    }
}
