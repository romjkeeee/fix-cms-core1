<?php

namespace AltSolution\Admin\Http\Controllers\Auth;

use AltSolution\Admin\Modules\User\UserModelInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ImpersonateController extends Controller
{
    use AuthorizesRequests;

    public function start($userId)
    {
        $this->authorize('permission', 'admin');
        $this->authorize('permission', 'user.impersonate');

        $user = app(UserModelInterface::class)->query()->find($userId);
        if (!$user) {
            abort(404, 'User not found');
        }

        $currentUser = auth()->user();
        session(['admin.impersonator_user' => $currentUser]);

        auth()->login($user);

        return redirect()->route('admin::index');
    }
    
    public function stop()
    {
        $user = session('admin.impersonator_user');
        if (!$user) {
            abort(404, 'User not found');
        }
        
        auth()->login($user);
        
        return redirect()->route('admin::index');
    }
}
