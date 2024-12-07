{{-- プロフィール編集画面 --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/address.css') }}" />
@endsection

@section('title', '住所の変更')

@section('content')
        <form method="POST" action="{{ route('update.purchase.address', ['item_id' => $item->id]) }}" class="address__update-container">
            @csrf
            @method('PATCH')
    {{-- 郵便番号 --}}
            <div class="form__group">
                <label for="postal_code">郵便番号</label>
                <input
                    type="text"
                    id="postal_code"
                    name="postal_code"
                    value="{{ old('postal_code', $address->postal_code) }}"
                    class="form__group-input"
                />
                @error('postal_code')
                    <p class="error-message">
                        {{ $message }}
                    </p>
                @enderror
            </div>
    {{-- 住所 --}}
            <div class="form__group">
                <label for="address">住所</label>
                <input
                    type="text"
                    id="address"
                    name="address"
                    value="{{ old('address', $address->address) }}"
                    class="form__group-input"
                />
                @error('address')
                    <p class="error-message">
                        {{ $message }}
                    </p>
                @enderror
            </div>
    {{-- 建物名 --}}
            <div class="form__group">
                <label for="building">建物名</label>
                <input
                    type="text"
                    id="building"
                    name="building"
                    value="{{ old('building', $address->building) }}"
                    class="form__group-input"
                />
                @error('building')
                    <p class="error-message">
                        {{ $message }}
                    </p>
                @enderror
            </div>
    {{-- 登録ボタン --}}
            <button type="submit" class="profile-form__button form__red-button">更新する</button>
        </form>

@endsection
