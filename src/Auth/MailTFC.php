<?php

namespace AltSolution\Admin\Auth;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Message;

class MailTFC implements TwoFactorCodeInterface
{
    public function generate()
    {
        return str_random();
    }

    public function send($user, $code)
    {
        $mailer = app(Mailer::class);
        $mailer->send('admin::auth.emails.f2a', compact('user', 'code'), function (Message $m) use ($user) {
            $m->subject(trans('admin::auth.f2a_subject'));
            $m->to($user->email);
        });
    }
}

