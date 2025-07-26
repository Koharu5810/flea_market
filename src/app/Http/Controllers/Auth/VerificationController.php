<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
// メール確認通知を表示
    public function notice()
    {
        $hideOnPages = true;  // ヘッダーボタン類非表示フラグ

        return view('auth.verify', compact('hideOnPages'));
    }

// 認証ユーザをメール認証ページへリダイレクト
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->route('profile.edit'); // 認証後のリダイレクト先
    }

// メール確認通知を再送信
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('home'); // 既に認証済みの場合のリダイレクト先
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', '確認メールが再送信されました');
    }
}
