<?php

namespace AltSolution\Admin\Http\Controllers\Auth;

use AltSolution\Admin\Auth\TwoFactorAuthInterface;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;

class F2AController extends Controller
{
    use ThrottlesLogins, AuthenticatesUsers;

    protected $redirectTo = '/admin';
    protected $redirectAfterLogout = '/admin';

    /**
     * @var TwoFactorAuthInterface
     */
    private $f2a;

    public function __construct(TwoFactorAuthInterface $f2a)
    {
        if (!$f2a->hasUser()) {
            abort(404, '2FA empty user');
        }
        $this->f2a = $f2a;
    }

    public function verify()
    {
        return view('admin::auth.f2a.verify');
    }

    public function verifyPost(Request $request)
    {
        $this->getValidationFactory()->extendImplicit('f2a', function ($attr, $value) {
            return $this->f2a->validate($value);
        });
        $this->validate($request, [
            'code' => 'required|f2a',
        ], [
            'f2a' => trans('admin::auth.f2a_invalid'),
        ]);

        $this->f2a->loginUser();
        $this->f2a->forgetUser();

        return $this->handleUserWasAuthenticated($request, true);
    }
}
