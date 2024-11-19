{{-- プロフィール設定画面 --}}
@extends('layouts.app')

@section('css')
{{-- 作成後indexCSSに変更 --}}
<link rel="stylesheet" href="{{ asset('css/profile/index.css') }}" />
@endsection

@section('title', 'プロフィール設定')

@section('content')
    <div class="profile-container">
    {{-- プロフィールヘッダー --}}
        <div class="profile__header">
            <img id="" src="" alt="プロフィールアイコン" style="display: none;" />
            <div class="profile-form__group">ユーザー名</div>
            <button>プロフィールを編集</button>
        </div>
    </div>
    <div class="profile__product">
        出品した商品
        購入した商品
    </div>
@endsection
