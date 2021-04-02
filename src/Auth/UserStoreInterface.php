<?php

namespace AltSolution\Admin\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

interface UserStoreInterface
{
    /**
     * @param Authenticatable $user
     * @param boolean$remember
     * @return void
     */
    public function put(Authenticatable $user, $remember);

    /**
     * @return boolean
     */
    public function hasUser();

    /**
     * @return void
     */
    public function forget();

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     * @return void
     */
    public function setCode($code);

    /**
     * @return Authenticatable
     */
    public function getUser();

    /**
     * @return bool
     */
    public function getRemember();
}
