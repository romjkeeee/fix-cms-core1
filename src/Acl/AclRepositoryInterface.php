<?php

namespace AltSolution\Admin\Acl;

interface AclRepositoryInterface
{
    function userHasPermission($userId, $permission);
}
