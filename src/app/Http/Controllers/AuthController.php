<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
// 会員登録画面表示
    public function showRegistrationForm() {
        return view('auth.register');
    }
// 会員登録処理
    public function register(RegisterRequest $request) {
        $form = $request->validated();

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 登録直後にログイン状態にする
        auth()->login($user);

        session(['username' => $form['username']]);

        return redirect()->route('profile.index');
    }

// ログイン画面表示
    public function showLoginForm() {
        return view('auth.login');
    }
// ログイン処理
    public function login(LoginRequest $request) {
            $user = User::where('username', $request->username)
                ->orWhere('email', $request->username)  // ユーザ名またはメールアドレスで認証
                ->first();

            // パスワードが一致するか確認
            if ($user && Hash::check($request->password, $user->password)) {
                auth()->login($user);  // 認証成功

                // 認証成功後商品一覧画面にリダイレクト
                return redirect()->route('home');
            }

            session()->flash('auth_error', 'ログイン情報が登録されていません。');
            return redirect()->route('login')->withInput();  // 入力値を保持してログイン画面にリダイレクト
    }
}
