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
        <div class="item__header">
            <a href="{{ route('profile.mypage', ['tab' => 'sell']) }}" class="item__tab {{ $tab === 'sell' ? 'active' : '' }}">
                <h2>出品した商品</h2>
            </a>
            <a href="{{ route('profile.mypage', ['tab' => 'buy']) }}" class="item__tab {{ $tab === 'buy' ? 'active' : '' }}">
                <h2>購入した商品</h2>
            </a>
        </div>
        <hr class="item__divider" />
    {{-- アイテムリスト（タブ表示） --}}
        <div class="item__main">
            @if ($items->isEmpty())
                <p>
                    @if ($tab === 'buy')
                        購入した商品がありません
                    @elseif ($tab === 'sell')
                        出品した商品がありません
                    @endif
                </p>
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
