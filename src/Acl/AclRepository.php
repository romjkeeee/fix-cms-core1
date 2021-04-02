<?php

namespace AltSolution\Admin\Acl;

class AclRepository implements AclRepositoryInterface
{
    private $userPermissions = [];

    // todo: cache
    function userHasPermission($user, $permission)
    {
        // inactive users has no permissions
        if ($user->active != 1) {
            return false;
        }
        // TODO: superuser has all perms
        if (!isset($this->userPermissions[$user->id])) {
            $permissions = $user->acl_role
                ->permissions()
                ->get()
                ->pluck('name')
                ->toArray();
            $this->userPermissions[$user->id] = $permissions;
        }

        return in_array($permission, $this->userPermissions[$user->id]);
    }
}