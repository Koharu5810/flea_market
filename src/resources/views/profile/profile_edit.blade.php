{{-- プロフィール編集画面 --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/profile_edit.css') }}" />
@endsection

@section('title', 'プロフィール設定')

@section('content')
    <form method="post" action="{{ route('profile.save') }}" enctype="multipart/form-data" class="profile__edit-container">
    @csrf
{{-- プロフィール画像 --}}
        <div class="profile__image">
            <div class="profile__image-preview" id="imagePreview">
                <img id="previewImage"
                    src="{{ isset($profileImage) ? $profileImage : '' }}"
                    alt="プロフィールアイコン"
                    style="{{ isset($profileImage) ? '' : 'display: none;' }}" />
            </div>
            <label class="edit__red-button">
                画像を選択する
                <input
                    type="file"
                    name="profile_image"
                    id="imageInput"
                    accept="image/*"
                    style="display: none;"
                />
            </label>
        </div>
        @error('profile_image')
            <div class="error-message">
                {{ $message }}
            </div>
        @enderror
{{-- ユーザー名 --}}
        <div class="form__group">
            <label for="username">ユーザー名</label>
            <input
                type="text"
                name="username"
                value="{{ old('username', $username) }}"
                class="form__group-input"
            />
            @error('username')
                <div class="error-message">
                    {{ $message }}
                </div>
            @enderror
        </div>
{{-- 郵便番号 --}}
        <div class="form__group">
            <label for="postal_code">郵便番号</label>
            <input
                type="text"
                name="postal_code"
                value="{{ old('postal_code', $userAddress->postal_code) }}"
                class="form__group-input"
            />
            @error('postal_code')
                <div class="error-message">
                    {{ $message }}
                </div>
            @enderror
        </div>
{{-- 住所 --}}
        <div class="form__group">
            <label for="address">住所</label>
            <input
                type="text"
                name="address"
                value="{{ old('address', $userAddress->address) }}"
                class="form__group-input"
            />
            @error('address')
                <div class="error-message">
                    {{ $message }}
                </div>
            @enderror
        </div>
{{-- 建物名 --}}
        <div class="form__group">
            <label for="building">建物名</label>
            <input
                type="text"
                name="building"
                value="{{ old('building', $userAddress->building) }}"
                class="form__group-input"
            />
            @error('building')
                <div class="error-message">
                        {{ $message }}
                </div>
            @enderror
        </div>
        {{-- 更新ボタン --}}
        <button class="profile-form__button form__red-button">更新する</button>
    </form>

<script>
    const imageInput = document.getElementById('imageInput');
    const previewImage = document.getElementById('previewImage');

    imageInput.addEventListener('change', function () {
        const file = this.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
            };

            reader.readAsDataURL(file);
        }
    });
</script>

@endsection
