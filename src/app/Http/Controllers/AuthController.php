<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function showRegistrationForm(RegisterRequest $request) {
        $form = $request->only([
            'username', 'email', 'password',
        ]);

        User::create($form);

        return view('auth.register');
    }

    public function showLoginForm() {

        return view('auth.login');
    }
}
