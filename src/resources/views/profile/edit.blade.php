{{-- プロフィール編集画面 --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/edit.css') }}" />
@endsection

@section('title', 'プロフィール設定')

@section('content')
    <div class="profile__create-container">
        <form method="post" action="{{ route('profile.save') }}" enctype="multipart/form-data">
        @csrf
        {{-- @if($isUpdate)
            @method('PATCH')
        @endif --}}
    {{-- プロフィール画像 --}}
            <div class="profile__image">
                <div class="profile__image-preview" id="imagePreview">
                    <img id="previewImage" src="" alt="プロフィールアイコン" style="display: none;" />
                </div>
                <label class="upload-button">
                    画像を選択する
                    <input type="file" name="profile_image" id="imageInput" accept="image/*" style="display: none;">
                </label>
            </div>
            @error('profile_image')
                <div class="profile-form__error">
                    {{ $message }}
                </div>
            @enderror
    {{-- ユーザー名 --}}
            <div class="profile-form__group">
                <label for="username">ユーザー名</label>
                <input type="text" name="username" value="{{ old('username', $username) }}" class="profile-form__group-input" />
                @error('username')
                    <div class="profile-form__error">
                        {{ $message }}
                    </div>
                @enderror
            </div>
    {{-- 郵便番号 --}}
            <div class="profile-form__group">
                <label for="postal_code">郵便番号</label>
                <input type="text" name="postal_code" value="{{ old('postal_code', $postal_code) }}" class="profile-form__group-input" />
                @error('postal_code')
                    <div class="profile-form__error">
                        {{ $message }}
                    </div>
                @enderror
            </div>
    {{-- 住所 --}}
            <div class="profile-form__group">
                <label for="address">住所</label>
                <input type="text" name="address" value="{{ old('address', $address) }}" class="profile-form__group-input" />
                @error('address')
                    <div class="profile-form__error">
                        {{ $message }}
                    </div>
                @enderror
            </div>
    {{-- 建物名 --}}
            <div class="profile-form__group">
                <label for="building">建物名</label>
                <input type="text" name="building" value="{{ old('building', $building) }}" class="profile-form__group-input" />
                @error('building')
                    <div class="profile-form__error">
                            {{ $message }}
                    </div>
                @enderror
            </div>
            {{-- 登録ボタン --}}
            <div class="profile-form__button">
                <button>更新する</button>
            </div>
        </form>
    </div>

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
