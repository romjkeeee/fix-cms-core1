<?php

namespace AltSolution\Admin\Http\Controllers;

use AltSolution\Admin\Auth\QRCodeInterface;
use AltSolution\Admin\Auth\TwoFactorAuthInterface;
use AltSolution\Admin\Modules\User\UserModelInterface;
use Illuminate\Http\Request;

class TFAController extends Controller
{
    public function index(QRCodeInterface $QRCode, $userId)
    {
        $this->authorize('permission', 'user.2fa');

        $user = app(UserModelInterface::class)->query()->findOrFail($userId);
        $secret = $user->otp_secret;
        $enabled = !empty($secret);
        if ($enabled) {
            $image = $QRCode->getQRCodeUrl($user->email, $secret);
        }

        $this->layout
            ->setActiveSection('users')
            ->setTitle(trans('admin::user.2fa'))
            ->addBreadcrumb(trans('admin::user.title'), route('admin::user_list'))
            ->addBreadcrumb(trans('admin::user.edit'), route('admin::user_edit', $userId));
        return view('admin::users.2fa', compact('user', 'enabled', 'secret', 'image'));
    }

    public function action(Request $request, TwoFactorAuthInterface $tfaAuth, $userId)
    {
        $this->authorize('permission', 'user.2fa');

        $user = app(UserModelInterface::class)->query()->findOrFail($userId);

        $action = $request->input('action');
        if ($action == 'enable') {
            $user->otp_secret = $tfaAuth->generateSecret();
            $user->save();
        } elseif ($action == 'disable') {
            $user->otp_secret = '';
            $user->save();
        }

        return redirect()->route('admin::user_2fa', $user['id']);
    }
}
