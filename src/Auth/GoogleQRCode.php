<?php

namespace AltSolution\Admin\Auth;

use PragmaRX\Google2FA\Google2FA;

class GoogleQRCode implements QRCodeInterface
{
    /**
     * @var Google2FA
     */
    private $googleTFA;

    public function __construct(Google2FA $googleTFA)
    {
        $this->googleTFA = $googleTFA;
    }

    public function getQRCodeUrl($holder, $secret)
    {
        return $this->googleTFA->getQRCodeGoogleUrl(url('/'), $holder, $secret);
    }
}
