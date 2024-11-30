{{-- プロフィール編集画面 --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/address.css') }}" />
@endsection

@section('title', '住所の変更')

@section('content')
    <div class="profile__create-container">
        <form method="POST" action="{{ route('update.purchase.address', ['item_id' => $item->id]) }}">
            @csrf
            @method('PATCH')
    {{-- 郵便番号 --}}
            <div class="profile-form__group">
                <label for="postal_code">郵便番号</label>
                <input
                    type="text"
                    id="postal_code"
                    name="postal_code"
                    value="{{ old('postal_code', $address->postal_code) }}"
                    class="profile-form__group-input"
                />
            </div>
            @error('postal_code')
                <p class="sell-form__error">
                    {{ $message }}
                </p>
            @enderror
    {{-- 住所 --}}
            <div class="profile-form__group">
                <label for="address">住所</label>
                <input
                    type="text"
                    id="address"
                    name="address"
                    value="{{ old('address', $address->address) }}"
                    class="profile-form__group-input"
                />
            </div>
            @error('address')
                <p class="sell-form__error">
                    {{ $message }}
                </p>
            @enderror
    {{-- 建物名 --}}
            <div class="profile-form__group">
                <label for="building">建物名</label>
                <input
                    type="text"
                    id="building"
                    name="building"
                    value="{{ old('building', $address->building) }}"
                    class="profile-form__group-input"
                />
            </div>
            @error('building')
                <p class="sell-form__error">
                    {{ $message }}
                </p>
            @enderror
    {{-- 登録ボタン --}}
            <button type="submit" class="profile-form__button red-button">更新する</button>
        </form>
    </div>

@endsection
