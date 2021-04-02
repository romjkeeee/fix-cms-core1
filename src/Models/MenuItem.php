<?php

namespace AltSolution\Admin\Models;

use AltSolution\Admin\Helpers\TranslateTrait;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use TranslateTrait;
    
	protected $table = 'menus_items';
    public $timestamps = false;

    protected $fillable = [
        'menu_id',
        'parent_id',
        'name_*',
        'url',
        'target'
    ];

    public function scopeParent($query, $id = 0)
    {
        return $query->where('parent_id', '=', '0')->where('id', '!=', $id);
    }

    public function child()
    {
        return $this->hasMany($this, 'parent_id')->orderBy('sort');
    }

    public function parent()
    {
        return $this->belongsTo($this, 'parent_id');
    }
}
