<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    public function showRegistrationForm() {
        return view('auth.register');
    }
    public function register(RegisterRequest $request) {
        $form = $request->validated();

        User::create($form);

        return redirect()->route('profile.index')->with('form', $form);
    }

    public function showLoginForm() {
        return view('auth.login');
    }
    public function login(LoginRequest $request) {
        return view('auth.login');
    }
}
