<?php

namespace AltSolution\Admin\Auth;

interface QRCodeInterface
{
    /**
     * @param string $holder
     * @param string $secret
     * @return string
     */
    public function getQRCodeUrl($holder, $secret);
}
