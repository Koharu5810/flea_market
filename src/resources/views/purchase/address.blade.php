{{-- プロフィール編集画面 --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/address.css') }}" />
@endsection

@section('title', '住所の変更')

@section('content')
    <div class="profile__create-container">
        <form method="POST" action="{{ route('redirect.purchase', ['item_id' => $item->id]) }}">
            @csrf
    {{-- 郵便番号 --}}
            <div class="profile-form__group">
                <label for="postal_code">郵便番号</label>
                <input
                    type="text"
                    name="postal_code"
                    value="{{ old('postal_code', $address->postal_code) }}"
                    class="profile-form__group-input"
                />
            </div>
    {{-- 住所 --}}
            <div class="profile-form__group">
                <label for="address">住所</label>
                <input
                    type="text"
                    name="address"
                    value="{{ old('address', $address->address) }}"
                    class="profile-form__group-input"
                />
            </div>
    {{-- 建物名 --}}
            <div class="profile-form__group">
                <label for="building">建物名</label>
                <input
                    type="text"
                    name="building"
                    value="{{ old('building', $address->building) }}"
                    class="profile-form__group-input"
                />
            </div>
    {{-- 登録ボタン --}}
            <button class="profile-form__button red-button">更新する</button>
        </form>
    </div>

@endsection
