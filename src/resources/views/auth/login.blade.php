{{-- ログイン画面 --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}" />
@endsection

@section('title', 'ログイン')

@section('content')
    <div class="login-container">
        <form method="POST" action="/login">
        @csrf
    {{-- ユーザー名/メールアドレス --}}
            <div class="login-form__group">
                <label for="username">ユーザー名/メールアドレス</label>
                <input type="text" name="username" value="{{ old('username') }}" class="login-form__group-input" />
                <div class="login-form__error">
                    @error('username')
                        {{ $message }}
                    @enderror
                </div>
            </div>
    {{-- パスワード --}}
            <div class="login-form__group">
                <label for="password">パスワード</label>
                <input type="password" name="password"  class="login-form__group-input" />
                <div class="login-form__error">
                    @error('password')
                        {{ $message }}
                    @enderror
                    @if (session('auth_error'))
                        {{ session('auth_error') }}
                    @endif
                </div>
            </div>
    {{-- ログインボタン --}}
            <button class="login-form__button red-button">ログインする</button>
        </form>
    {{-- 会員登録案内 --}}
        <a href="/register" class="register-button blue-button">会員登録はこちら</a>
    </div>
@endsection
