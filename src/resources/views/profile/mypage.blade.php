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
        <div class="profile__meta">
            <div class="profile__user">
                <p class="username">{{ $user->username }}</p>

                @if ($user->rating_count > 0)
                    @php
                        $average = round($user->rating_total / $user->rating_count);
                    @endphp

                    <div class="star-display">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="{{ $i <= $average ? 'filled' : 'empty' }}">★</span>
                        @endfor
                    </div>
                @endif
            </div>

            <a href="{{ route('profile.edit') }}" class="edit__red-button edit-button">プロフィールを編集</a>
        </div>
    </div>

{{-- タブ表示 --}}
    <div class="item__header">
        <a href="{{ route('profile.mypage', ['tab' => 'sell']) }}" class="item__tab {{ $tab === 'sell' ? 'active' : '' }}">
            <h2 class="pc-text">出品した商品</h2>
            <h2 class="sp-text">出品</h2>
        </a>
        <a href="{{ route('profile.mypage', ['tab' => 'buy']) }}" class="item__tab {{ $tab === 'buy' ? 'active' : '' }}">
            <h2 class="pc-text">購入した商品</h2>
            <h2 class="sp-text">購入</h2>
        </a>
        <a href="{{ route('profile.mypage', ['tab' => 'trading']) }}" class="item__tab {{ $tab === 'trading' ? 'active' : '' }}">
            <span class="item-title-with-badge">
                <h2 class="pc-text">取引中の商品</h2>
                <h2 class="sp-text">取引中</h2>
                @if ( $unreadMessageCount > 0)
                    <span class="total-unread-badge" data-count="{{ $unreadMessageCount }}">
                        {{ $unreadMessageCount > 99 ? '99+' : $unreadMessageCount }}
                    </span>
                @endif
            </span>
        </a>
    </div>

    <hr class="item__divider" />

{{-- アイテムリスト（タブ切り替え） --}}
    <div class="item__main">
        @if ($displayItems->isEmpty())
            <p>
                @switch($tab)
                    @case('buy') 購入した商品がありません @break
                    @case('sell') 出品した商品がありません @break
                    @case('trading') 取引中の商品がありません @break
                @endswitch
            </p>
        @else
            @foreach ($displayItems as $entry)
                @php
                    $item = $entry['item'];
                    $link = $entry['link'];
                    $unreadCount = $entry['unread_count'];
                @endphp

                <a href="{{ $link }}" class="item-link">
                    <div class="item-container">
                        <div class="item-image">
                            @if ($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
                            @else
                                商品画像
                            @endif

                            {{-- 未読バッジ --}}
                            @if (($unreadCount ?? 0) > 0)
                                <span class="unread-badge {{ ($unreadCount ?? 0) > 99 ? 'large-count' : '' }}">
                                    {{ ($unreadCount ?? 0) > 99 ? '99+' : $unreadCount }}
                                </span>
                            @endif
                        </div>

                        <div class="item-name">{{ $item->name }}</div>
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
