{{-- プロフィール設定画面 --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/index.css') }}" />
@endsection

@section('content')
    <div class="profile-container">
    {{-- プロフィールヘッダー --}}
        <div class="profile__header">
            <div class="profile__image" id="imagePreview">
                @if (!empty($profileImage))
                    <img id="previewImage"
                        src="{{ $profileImage }}"
                        alt="プロフィールアイコン" />
                @else
                    <div class="profile__image"></div>
                @endif
            </div>
            <div class="profile__username">{{ $user->username }}</div>
            <button class="profile__edit-button"><a href="{{ route('profile.edit') }}">プロフィールを編集</a></button>
        </div>
    </div>
    <div class="item__header">
        <span>出品した商品</span>
        <span>購入した商品</span>
    </div>
    <div class="item__main">
        <div class="item-container">
            <div class="item-image">商品画像</div>
            <div class="item-name">商品名</div>
        </div>
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
