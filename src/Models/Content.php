<?php

namespace AltSolution\Admin\Models;

use AltSolution\Admin\Helpers\SeoInterface;
use AltSolution\Admin\Helpers\SeoTrait;
use AltSolution\Admin\Helpers\TranslateInterface;
use AltSolution\Admin\Helpers\TranslateTrait;
use AltSolution\Admin\Modules\Content\ContentModelInterface;
use Illuminate\Database\Eloquent\Model;

class Content extends Model implements TranslateInterface, SeoInterface, ContentModelInterface
{
    use TranslateTrait;
    use SeoTrait;

    protected $table = 'contents';
    protected $fillable = [
        'title_*',
        'content_*',
        // todo: rename to slug
        'url',
        // todo: rename to is_published or is_active
        'active',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // TODO: does this required?
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}
