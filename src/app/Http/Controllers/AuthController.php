<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
// 会員登録画面表示
    public function showRegistrationForm() {
        return view('auth.register');
    }
// 会員登録処理
    public function register(RegisterRequest $request) {
        $form = $request->validated();

        User::create($form);

        return redirect()->route('profile.index')->with('form', $form);
    }

// ログイン画面表示
    public function showLoginForm() {
        return view('auth.login');
    }
// ログイン処理
    public function login(LoginRequest $request) {
        return view('auth.login');
    }
}
