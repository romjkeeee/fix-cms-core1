<?php

namespace AltSolution\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class AclPermission extends Model
{
    protected $table = 'acl_permissions';
    public $timestamps = false;

    protected $fillable = [
    	'name',
    	'description',
	];

	public function roles()
    {
        return $this->belongsToMany(AclRole::class, 'acl_permissions_acl_roles', 'acl_permission_id', 'acl_role_id');
    }
}
