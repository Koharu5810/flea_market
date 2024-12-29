<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // ユーザーがログインしていない、またはメールが未認証の場合
        if (! $request->user() || ! $request->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice'); // メール認証ページにリダイレクト
        }

        return $next($request);
    }
}
