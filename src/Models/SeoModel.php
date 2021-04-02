<?php

namespace AltSolution\Admin\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $model_name
 * @property int $model_id
 * @property string $locale
 * @property string $key
 * @property string $value
 */
class SeoModel extends Model
{
    protected $table = 'seo_models';
    protected $fillable = [
        'model_name',
        'model_id',
        'locale',
        'key',
        'value'
    ];
}
