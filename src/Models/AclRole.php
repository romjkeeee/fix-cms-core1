<?php

namespace AltSolution\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class AclRole extends Model
{
    protected $table = 'acl_roles';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(AclPermission::class, 'acl_permissions_acl_roles', 'acl_role_id', 'acl_permission_id');
    }
}
