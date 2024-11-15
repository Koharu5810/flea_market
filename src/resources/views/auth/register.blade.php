@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}" />
@endsection

@section('content')
    <div class="register-container">
        <h1 class="form__title">会員登録</h1>
        <form method="" action="">
        @csrf
            {{-- ユーザー名 --}}
            <div class="form__group">
                <label for="username">ユーザー名</label>
                <input type="text" name="username" placeholder="" value="{{ old('username') }}" class="form__group-input" />
                <div class="form__error">
                    @error('username')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            {{-- メールアドレス --}}
            <div class="form__group">
                <label for="username">メールアドレス</label>
                <input type="email" name="email" placeholder="" value="{{ old('email') }}" class="form__group-input" />
                <div class="form__error">
                    @error('email')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            {{-- パスワード --}}
            <div class="form__group">
                <label for="password">ユーザー名</label>
                <input type="password" name="password" placeholder=""  class="form__group-input" />
                <div class="form__error">
                    @error('password')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            {{-- 確認用パスワード --}}
            <div class="form__group">
                <label for="password">確認用パスワード</label>
                <input type="password" name="password_confirmation" placeholder="" class="form__group-input" />
                <div class="form__error">
                    @error('username')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            {{-- 登録ボタン --}}
            <div class="form__button">
                <button>登録する</button>
            </div>
        </form>
        {{-- ログイン案内 --}}
        <div class="login">
            <a href="" class="login-button">ログインはこちら</a>
        </div>
    </div>
@endsection
