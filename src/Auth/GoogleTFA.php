<?php

namespace AltSolution\Admin\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use PragmaRX\Google2FA\Google2FA;

class GoogleTFA extends AbstractTFA
{
    /**
     * @var Google2FA
     */
    private $googleTFA;

    public function __construct(UserStoreInterface $userStore, Google2FA $googleTFA)
    {
        $this->userStore = $userStore;
        $this->googleTFA = $googleTFA;
    }

    public function isEnabledFor(Authenticatable $user)
    {
        // if has secret
        return !empty($user->otp_secret);
    }

    public function validate($code)
    {
        $user = $this->userStore->getUser();
        return $this->googleTFA->verifyKey($user->otp_secret, $code);
    }

    public function requireSecret()
    {
        return true;
    }

    public function generateSecret()
    {
        return $this->googleTFA->generateSecretKey(16);
    }
}
