{{-- ログイン画面 --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}" />
@endsection

@section('title', 'ログイン')

@section('content')
    <form method="POST" action="/login" class="login-container">
    @csrf
{{-- ユーザー名/メールアドレス --}}
        <div class="form__group">
            <label for="username">ユーザー名/メールアドレス</label>
            <input type="text" name="username" value="{{ old('username') }}" class="form__group-input" />
            <div class="error-message">
                @error('username')
                    {{ $message }}
                @enderror
            </div>
        </div>
{{-- パスワード --}}
        <div class="form__group">
            <label for="password">パスワード</label>
            <input type="password" name="password"  class="form__group-input" />
            <div class="error-message">
                @error('password')
                    {{ $message }}
                @enderror
                @if (session('auth_error'))
                    {{ session('auth_error') }}
                @endif
            </div>
        </div>
{{-- ログインボタン --}}
        <button class="login-form__button form__red-button">ログインする</button>
    </form>
{{-- 会員登録案内 --}}
    <a href="/register" class="register-link blue-button">会員登録はこちら</a>
@endsection
