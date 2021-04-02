<?php

namespace AltSolution\Admin\Models;

use AltSolution\Admin\Helpers\ImagesInterface;
use AltSolution\Admin\Modules\User\UserModelInterface;
use Illuminate\Foundation\Auth\User as Authenticatable;
use AltSolution\Admin\Helpers\ImagesTrait;

class User extends Authenticatable implements ImagesInterface, UserModelInterface
{
    use ImagesTrait;

    public function getImagesFields()
    {
        return [
            // TODO: @new_version rename to avatar
            'avatar_file' => [
                'list' => ['crop', 200, 200],
            ],
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'acl_role_id',
        // todo: @new_version rename to is_active
        'active'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // TODO: @new_version rename to aclRole
    public function acl_role()
    {
        return $this->belongsTo(AclRole::class);
    }
}
