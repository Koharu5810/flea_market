{{-- プロフィール設定画面 --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/mypage.css') }}" />
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
        <h2>出品した商品</h2>
        <h2>購入した商品</h2>
    </div>
    <hr class="item__divider" />
    <div class="item__main">
        @if ($items->isEmpty())
            <p>購入した商品がありません</p>
        @else
            @foreach ($items as $item)
                <a href="{{ route('item.detail', ['id' => $item->id]) }} " class="item-link">
                    <div class="item-container">
                        @if ($item->image)
                            <div class="item-image">
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" />
                            </div>
                        @else
                            <div class="item-image">商品画像</div>
                        @endif
                        <div class="item-name">
                            {{ $item->name }}
                        </div>
                    </div>
                </a>
            @endforeach
        @endif
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
