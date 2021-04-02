<?php

namespace AltSolution\Admin\Http\Controllers;

use AltSolution\Admin\Forms\RoleForm;
use AltSolution\Admin\Models\AclRole;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function getRoles()
    {
        $this->authorize('permission', 'role.list');

        $roles = AclRole::query()
            ->paginate(config('admin.item_per_page', 20));
        $user = auth()->user();

        $this->layout
            ->setActiveSection('users')
            ->setTitle(trans('admin::user.role_title'));
        return view('admin::users.roles', ['acl_roles' => $roles, 'user' => $user]);
    }

    public function getEdit($id = null)
    {
        $this->authorize('permission', 'role.edit');

        $role = AclRole::query()->findOrNew($id);
        $form = cms_create_form(RoleForm::class, $role);

        $this->layout
            ->setActiveSection('users')
            ->setTitle(trans($role ? 'admin::user.role_edit' : 'admin::user.role_add'))
            ->addBreadcrumb(trans('admin::user.role_title'), route('admin::user_roles'));
        return view('admin::users.role_edit', compact('role', 'form'));
    }

    public function postRoles(Request $request)
    {
        $this->authorize('permission', 'role.edit');

        $this->validate($request, [
            'name' => 'required|alpha',
        ]);

        $role = AclRole::query()->firstOrNew(['id' => $request['id']]);
        $role->fill($request->all());
        $role->save();

        $this->layout->addNotify('success', trans('admin::user.role_saved'));

        if ($request['button_apply']) {
            return redirect()->route('admin::user_editrole', ['id' => $role['id']]);
        }

        return redirect()->route('admin::user_roles');
    }

    public function action(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids');
        if (!$ids) {
            return;
        }

        if ($action == 'delete') {
            $this->authorize('permission', 'role.delete');

            $currentUser = auth()->user();
            foreach ($ids as $id) {
                /** @var Model $role */
                $role = AclRole::query()->find($id);
                if (!$role && $role->id == $currentUser->acl_role_id) {
                    continue;
                }
                $role->delete();
            }
        }
    }
}
