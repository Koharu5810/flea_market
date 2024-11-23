{{-- 会員登録画面 --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css') }}" />
@endsection

@section('title', '会員登録')

@section('content')
    <div class="register-container">
        <form method="post" action="/register">
        @csrf
    {{-- ユーザー名 --}}
            <div class="register-form__group">
                <label for="username">ユーザー名</label>
                <input type="text" name="username" value="{{ old('username') }}" class="register-form__group-input" />
                <div class="register-form__error">
                    @error('username')
                        {{ $message }}
                    @enderror
                </div>
            </div>
    {{-- メールアドレス --}}
            <div class="register-form__group">
                <label for="username">メールアドレス</label>
                <input type="email" name="email" value="{{ old('email') }}" class="register-form__group-input" />
                <div class="register-form__error">
                    @error('email')
                        {{ $message }}
                    @enderror
                </div>
            </div>
    {{-- パスワード --}}
            <div class="register-form__group">
                <label for="password">パスワード</label>
                <input type="password" name="password"  class="register-form__group-input" />
                <div class="register-form__error">
                    @error('password')
                        {{ $message }}
                    @enderror
                </div>
            </div>
    {{-- 確認用パスワード --}}
            <div class="register-form__group">
                <label for="password_confirmation">確認用パスワード</label>
                <input type="password" name="password_confirmation" class="register-form__group-input" />
                <div class="register-form__error">
                    @error('password_confirmation')
                        {{ $message }}
                    @enderror
                </div>
            </div>
    {{-- 登録ボタン --}}
            <div class="register-form__button">
                <button>登録する</button>
            </div>
        </form>
{{-- ログイン案内 --}}
        <div class="login-container">
            <a href="/login" class="login-button">ログインはこちら</a>
        </div>
    </div>
@endsection
