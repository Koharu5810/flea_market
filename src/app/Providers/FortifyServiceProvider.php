<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Hash;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);

        // /registerにアクセスした時に表示するviewファイルの指定
        Fortify::registerView(function () {
            return view('auth.register');
        });
        // /loginにアクセスした時に表示するviewファイル
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // ログイン処理
        Fortify::authenticateUsing(function (LoginRequest $request) {
            $user = User::where('username', $request->username)
                        ->orWhere('email', $request->username)  // ユーザ名またはメールアドレスで認証
                        ->first();

            // パスワードが一致するか確認
            if ($user && Hash::check($request->password, $user->password)) {
                return $user;  // 認証成功
            }
            return null;  // 認証失敗
        });

        // ログイン試行回数を制限する設定
        RateLimiter::for('login', function (Request $request) {
            $username = (string) $request->input('username');

            return Limit::perMinute(5)->by($username . '|' . $request->ip());
        });

        // ログイン後のリダイレクト先
        Fortify::redirects('login', '/');
    }
}
