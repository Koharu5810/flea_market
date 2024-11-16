{{-- プロフィール設定画面 --}}
@extends('layouts.app')

@section('css')
{{-- 作成後indexCSSに変更 --}}
<link rel="stylesheet" href="{{ asset('css/register.css') }}" />
@endsection

@section('title', 'プロフィール設定')

@section('content')
    <div class="register-container">
        <form method="post" action="">
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
                <label for="postal_code">郵便番号</label>
                <input type="text" name="postal_code" value="{{ old('postal_code') }}" class="form__group-input" />
                <div class="form__error">
                    @error('postal_code')
                        {{ $message }}
                    @enderror
                </div>
            </div>
    {{-- 住所 --}}
            <div class="form__group">
                <label for="address">住所</label>
                <input type="text" name="address"  class="form__group-input" />
                <div class="form__error">
                    @error('address')
                        {{ $message }}
                    @enderror
                </div>
            </div>
    {{-- 建物名 --}}
            <div class="form__group">
                <label for="building">建物名</label>
                <input type="text" name="building" class="form__group-input" />
                <div class="form__error">
                    @error('building')
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
