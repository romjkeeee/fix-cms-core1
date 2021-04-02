<?php

namespace AltSolution\Admin\System;

class Notify
{
    const SESSION_KEY = 'cms_notify';

    function add($type, $message)
    {
        if (!in_array($type, ['error', 'warning', 'success', 'info'])) {
            throw new \Exception('Invalid notify type: ' . $type);
        }
        $session = app('session');

        $notifications = $session->get(self::SESSION_KEY, []);
        $notifications[] = [$type, $message];
        $session->flash(self::SESSION_KEY, $notifications);
    }

    function getAll()
    {
        $session = app('session');

        $notifications = $session->get(self::SESSION_KEY, []);
        return $notifications;
    }
}