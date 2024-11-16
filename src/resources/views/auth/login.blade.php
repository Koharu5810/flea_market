@extends('layouts.app')

@section('css')
{{-- 作成後loginCSSに変更 --}}
<link rel="stylesheet" href="{{ asset('css/register.css') }}" />
@endsection

@section('title', 'ログイン')

@section('content')
    <div class="register-container">
        <form method="post" action="/login">
        @csrf
            {{-- ユーザー名/メールアドレス --}}
            <div class="form__group">
                <label for="username">ユーザー名/メールアドレス</label>
                <input type="text" name="username" value="{{ old('username') }}" class="form__group-input" />
                <div class="form__error">
                    @error('username')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            {{-- パスワード --}}
            <div class="form__group">
                <label for="password">パスワード</label>
                <input type="password" name="password"  class="form__group-input" />
                <div class="form__error">
                    @error('password')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            {{-- ログインボタン --}}
            <div class="form__button">
                <button>ログインする</button>
            </div>
        </form>
        {{-- 会員登録案内 --}}
        <div class="login">
            <a href="/register" class="login-button">会員登録はこちら</a>
        </div>
    </div>
@endsection
