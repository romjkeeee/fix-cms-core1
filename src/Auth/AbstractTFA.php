<?php

namespace AltSolution\Admin\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

abstract class AbstractTFA implements TwoFactorAuthInterface
{
    /**
     * @var UserStoreInterface
     */
    protected $userStore;

    public function isEnabledFor(Authenticatable $user)
    {
        // enabled for everyone
        return true;
    }

    public function putUser(Authenticatable $user, $remember)
    {
        $this->userStore->put($user, $remember);
    }

    public function hasUser()
    {
        return $this->userStore->hasUser();
    }

    public function forgetUser()
    {
        $this->userStore->forget();
    }

    public function loginUser()
    {
        $user = $this->userStore->getUser();
        $remember = $this->userStore->getRemember();
        auth()->login($user, $remember);
    }

    public function sendVerification()
    {
        // NOP
    }

    abstract public function validate($code);

    public function requireSecret()
    {
        return false;
    }

    public function generateSecret()
    {
        // NOP
    }
}
