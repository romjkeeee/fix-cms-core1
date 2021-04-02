<?php

namespace AltSolution\Admin\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $page
 * @property string $locale
 * @property string $key
 * @property string $value
 */
class SeoPage extends Model
{
    protected $table = 'seo_pages';
    protected $fillable = [
        'page',
        'locale',
        'key',
        'value'
    ];
}
