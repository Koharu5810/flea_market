<?php

namespace App\Http\Controllers;

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

        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.index')->with('form', [
            'username' => $form['username'],
        ]);
    }

// ログイン画面表示
    public function showLoginForm() {
        return view('auth.login');
    }
// ログイン処理はFortifyServiceProvider.phpで定義
}
