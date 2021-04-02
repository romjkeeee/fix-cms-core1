<?php

namespace AltSolution\Admin\Auth;

interface TwoFactorCodeInterface
{
    /**
     * Generate code for Two-factor authentication
     * @return string
     */
    public function generate();

    /**
     * Send generated code to user
     * @param $user
     * @param string $code
     * @return void
     */
    public function send($user, $code);
}