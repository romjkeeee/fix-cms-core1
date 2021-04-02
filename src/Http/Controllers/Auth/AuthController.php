<?php

namespace AltSolution\Admin\Http\Controllers\Auth;

use AltSolution\Admin\Auth\TwoFactorAuthInterface;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use ThrottlesLogins, AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';
    protected $redirectAfterLogout = '/admin';
    /**
     * @var string
     */
    protected $loginView = 'admin::auth.login';

    /**
     * Create a new authentication controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    // TODO: not forget update this method on updating laravel version
    public function login(Request $request, TwoFactorAuthInterface $f2a)
    {
        $this->validateLogin($request);

        if ($lockedOut = $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->getCredentials($request);

        $guard = auth($this->getGuard());

        if ($guard->attempt($credentials, false, false)) {
            $user = $guard->getLastAttempted();

            $is2FAEnabled = config('admin.auth_2fa');
            if ($is2FAEnabled && $f2a->isEnabledFor($user)) {
                $f2a->putUser($user, $request->has('remember'));
                try {
                    $f2a->sendVerification();
                    return redirect()->route('admin::2fa');
                } catch (\Exception $e) {
                    $f2a->forgetUser();
                    return redirect()->back()
                        ->withInput($request->only($this->loginUsername(), 'remember'))
                        ->withErrors([
                            $this->loginUsername() => trans('admin::auth.f2a_sent_failed'),
                        ]);
                }
            } else {
                $guard->login($user, $request->has('remember'));
                return $this->handleUserWasAuthenticated($request, true);
            }
        }

        if (! $lockedOut) {
            $this->incrementLoginAttempts($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

}
