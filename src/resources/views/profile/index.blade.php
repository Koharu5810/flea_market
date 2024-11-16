{{-- プロフィール設定画面 --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}" />
@endsection

@section('title', 'プロフィール設定')

@section('content')
    <div class="register-container">
        <form method="post" action="/register">
        @csrf
    {{-- ユーザー名 --}}
            <div class="form__group">
                <label for="username">ユーザー名</label>
                <input type="text" name="username" value="{{ old('username') }}" class="form__group-input" />
                <div class="form__error">
                    @error('username')
                        {{ $message }}
                    @enderror
                </div>
            </div>
    {{-- 郵便番号 --}}
            <div class="form__group">
                <label for="username">メールアドレス</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form__group-input" />
                <div class="form__error">
                    @error('email')
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
    {{-- 確認用パスワード --}}
            <div class="form__group">
                <label for="password_confirmation">確認用パスワード</label>
                <input type="password" name="password_confirmation" class="form__group-input" />
                <div class="form__error">
                    @error('password_confirmation')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            {{-- 登録ボタン --}}
            <div class="form__button">
                <button>更新する</button>
            </div>
        </form>
    </div>
@endsection
