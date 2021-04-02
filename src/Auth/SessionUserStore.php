<?php

namespace AltSolution\Admin\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use AltSolution\Admin\Models\User;

class SessionUserStore implements UserStoreInterface
{
    private $loaded = false;

    private $userId;
    private $remember;
    private $code;
    private $attempts = 0;

    public function put(Authenticatable $user, $remember)
    {
        $this->userId = $user->id;
        $this->remember = $remember;
        $this->attempts = 0;
        $this->save();
    }

    public function hasUser()
    {
        if (!$this->loaded) {
            $this->load();
        }

        return $this->userId !== null;
    }

    public function forget()
    {
        $this->userId = null;
        $this->remember = null;
        $this->code = null;
        $this->attempts = 0;
        session()->forget('f2a');
    }

    public function getCode()
    {
        if (!$this->loaded) {
            $this->load();
        }

        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
        $this->save();
    }

    public function getUser()
    {
        if (!$this->loaded) {
            $this->load();
        }

        return User::query()->find($this->userId);
    }

    public function getRemember()
    {
        if (!$this->loaded) {
            $this->load();
        }

        return $this->remember;
    }

    private function save()
    {
        session()->set('f2a', [
            'user_id' => encrypt($this->userId),
            'remember' => $this->remember,
            'code' => encrypt($this->code),
            'attempts' => $this->attempts,
        ]);
//        $request->session()
    }

    private function load()
    {
        $f2a = session('f2a');
        if ($f2a !== null) {
            $userId = array_get($f2a, 'user_id');
            if ($userId) {
                $this->userId = decrypt($userId);
            }
            $this->remember = array_get($f2a, 'remember');
            $code = array_get($f2a, 'code');
            if ($code) {
                $this->code = decrypt($code);
            }
            $this->attempts = array_get($f2a, 'attempts');
        }
        $this->loaded = true;
    }
}
