<?php

namespace AltSolution\Admin\Http\Controllers;

use AltSolution\Admin\Helpers\ImagesInterface;
use AltSolution\Admin\Models\AclRole;
use AltSolution\Admin\Modules\User\UserFormInterface;
use AltSolution\Admin\Modules\User\UserModelInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function getProfile(Request $request)
    {
        $this->authorize('permission', 'user.edit');
        $userId = $request->user()->id;
        return redirect()->route('admin::user_edit', $userId);
    }

    public function getIndex(Request $request)
    {
        $this->authorize('permission', 'user.list');

        $filter = [
            'q' => $request->input('q'),
            'role' => $request->input('role'),
            //'sort' => $request->input('sort'),
        ];

        /** @var Builder $qb */
        $qb = app(UserModelInterface::class)->query();
        if (!empty($filter['q'])) {
            $search = trim($filter['q']);
            $qb->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%");
            });
        }
        if (!empty($filter['role'])) {
            $qb->where('acl_role_id', $filter['role']);
        }

        $users = $qb->orderBy('created_at', 'DESC')->paginate(config('admin.item_per_page', 20));
        $users->addQuery('q', $filter['q']);
        $users->addQuery('role', $filter['role']);
        //$users->addQuery('sort', $filter['sort']);
        $roles = AclRole::all();

        $this->layout
            ->setActiveSection('users')
            ->setTitle(trans('admin::user.title'));

        return view('admin::users.list', compact('users', 'roles', 'filter'));
    }

    public function getEdit($id = null)
    {
        $this->authorize('permission', 'user.edit');

        $user = null;
        if ($id) {
            $user = app(UserModelInterface::class)->query()->find($id);
        }
        $form = app(UserFormInterface::class)->create($user);

        $data = [
            'edit_user' => $user,
            'form' => $form,
        ];

        $this->layout
            ->setActiveSection('users')
            ->setTitle(trans($user ? 'admin::user.edit' : 'admin::user.add'))
            ->addBreadcrumb(trans('admin::user.title'), route('admin::user_list'));
        return view('admin::users.edit', $data);
    }

    public function postSave(Request $request)
    {
        $this->authorize('permission', 'user.edit');
        $userId = $request['id'];

        $validator = $this->validator($request->all(), $userId);
        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }

        /** @var Model $user */
        $user = app(UserModelInterface::class)->query()->firstOrNew(['id' => $userId]);
        $user->fill($request->all());
        if ($user instanceof ImagesInterface) {
            $user->imageAllSave($request);
        }

        if ($request['password']) {
    	   $user['password'] = bcrypt($request['password']);
        }

        $user->id == auth()->user()->id ? $user->active = 1 : null;

    	$user->save();

    	$this->layout->addNotify('success', trans('admin::user.saved'));

        if ($request['button_apply']) {
            return redirect()->route('admin::user_edit', ['id' => $user['id']]);
        }

    	return redirect()->route('admin::user_list');
    }

    /**
     * Get a validator for save request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data, $modelId = null)
    {
        return $this->getValidationFactory()->make($data, [
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email,' . $modelId,
            'avatar_file' => 'image|max:1000|image_size:>=128,>=128|image_size:<6000,<6000',
            'password' => array_merge($modelId ? [] : ['required'], [
                'confirmed',
                'min:6',
                'max:60',
                'regex:/[a-z]/', // letter lower
                'regex:/[A-Z]/', // letter upper
                'regex:/[0-9]/', // number
                'regex:/[!@#$%^&*?()\-_=+{};:,<.>]/' // special
            ]),
            'password_confirmation' => $modelId ? '' : 'required',
        ], [
            'password.regex' => trans('admin::user.password_sux'),
        ]);
    }

    public function action(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids');
        if (!$ids) {
            return;
        }
        
        if ($action == 'activate') {
            $this->authorize('permission', 'user.edit');

            foreach ($ids as $id) {
                /** @var Model $user */
                $user = app(UserModelInterface::class)->query()->find($id);
                if (!$user) {
                    continue;
                }
                $user->active = 1;
                $user->save();
            }
        }
        elseif ($action == 'deactivate') {
            $this->authorize('permission', 'user.edit');

            $currentUserId = auth()->user()->id;
            foreach ($ids as $id) {
                /** @var Model $user */
                $user = app(UserModelInterface::class)->query()->find($id);
                // do not allow deactivate himself
                if (!$user || $user->id == $currentUserId) {
                    continue;
                }
                $user->active = 0;
                $user->save();
            }
        }
        elseif ($action == 'delete') {
            $this->authorize('permission', 'user.delete');

            foreach ($ids as $id) {
                /** @var Model $user */
                $user = app(UserModelInterface::class)->query()->find($id);
                if (!$user) {
                    continue;
                }
                if ($user instanceof ImagesInterface) {
                    $user->imageAllDelete();
                }
                // TODO: seo, uploads, etc
                $user->delete();
            }
        }
    }

}
