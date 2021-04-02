<?php

namespace AltSolution\Admin\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

interface TwoFactorAuthInterface
{
    /**
     * Is two factor auth enabled for user
     * @param Authenticatable $user
     * @return boolean
     */
    public function isEnabledFor(Authenticatable $user);

    /**
     * Rememger user after first step
     * @param Authenticatable $user
     * @param boolean $remember
     * @return void
     */
    public function putUser(Authenticatable $user, $remember);

    /**
     * Verify that first step done
     * @return bool
     */
    public function hasUser();

    /**
     * Authentificate user
     * @return void
     */
    public function loginUser();

    /**
     * Forget everything about first step
     * @return void
     */
    public function forgetUser();

    /**
     * Send verification code to user
     * @return void
     */
    public function sendVerification();

    /**
     * Validate verification code
     * @param string $code
     * @return boolean
     */
    public function validate($code);

    /**
     * @return boolean
     */
    public function requireSecret();

    /**
     * @return string
     */
    public function generateSecret();


    /*
    function redirectNext();

    function getQrCodeUri();

    function getSecret();
    function getLabel();
    function getIssuer();

    public function hasSecret()
    {
        return true;
    }
    */
}