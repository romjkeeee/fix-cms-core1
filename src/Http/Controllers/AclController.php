<?php

namespace AltSolution\Admin\Http\Controllers;

use AltSolution\Admin\Jobs\AdminUpdatePermission;
use AltSolution\Admin\Models\AclPermission;
use AltSolution\Admin\Models\AclRole;
use Auth;
use Illuminate\Http\Request;

class AclController extends Controller
{
    public function getAcl()
    {
        $this->authorize('permission', 'role.edit');

        $this->dispatch(new AdminUpdatePermission());

        // todo: move to repository
        $roles = AclRole::all();
        $allPermissions = [];
        foreach (AclPermission::all() as $permission) {
            $allPermissions[$permission->name] = $permission;
        }

        $system = cms_system();

        $sections = [];
        foreach ($system->getPermission()->getSections() as $section) {

            $sectionPermissions = [];
            foreach ($section->getPermissions() as $permission) {
                $aclPermission = array_get($allPermissions, $permission->getName());
                if ($aclPermission !== null) {
                    $sectionPermissions[] = $aclPermission;
                }
            }

            if (count($sectionPermissions)) {
                $sections[] = [
                    'name' => $section->getDescription(),
                    'permissions' => $sectionPermissions
                ];
            }
        }

        $this->layout
            ->setActiveSection('users')
            ->setTitle(trans('admin::user.acl_permission'));
        return view('admin::users.acl', compact('roles', 'sections'));
    }

    public function postAcl(Request $request)
    {
        // todo: own permission
        $this->authorize('permission', 'role.edit');

        $data = $request['data'];
        $roles = AclRole::all();
        foreach ($roles as $role) {
            $role->permissions()->detach();
            if (isset($data[$role->id]) && is_array($data[$role->id]) && count($data[$role->id])) {
                $role->permissions()->attach($data[$role->id]);
            }
        }
    }
}
