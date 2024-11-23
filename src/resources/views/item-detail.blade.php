{{-- 商品詳細画面 --}}
@extends('layouts.app')

@section('css')
{{-- ファイル名変更 --}}
<link rel="stylesheet" href="{{ asset('css/item-detail.css') }}" />
@endsection

@section('content')
    <div class="item-container">
    {{-- 左側 --}}
        <div class="left-container">
            @if ($item->image)
                <div class="item-image">
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" />
                </div>
            @else
                <div class="item-image">商品画像</div>
            @endif
        </div>
    {{-- 右側 --}}
        <div class="right-container">
            <div class="item-detail">
                <h2 class="item-name">{{ $item->name }}</h2>
                <p class="item-brand">{{ $item->name }}</p>
                <p class="item-price">&yen;<span>{{ number_format($item->price) }}</span> (税込)</p>
                <div class="item-status">☆   ◯</div>
            </div>
            {{-- <form method="post" action="">
                @csrf --}}
                <button type="submit" class="purchase-button">購入手続きへ</button>
            {{-- </form> --}}
            <div class="item-description">
                <h3 class="item__title">商品説明</h3>
                {{ $item->description }}
                <h3 class="item__title">商品の情報</h3>
                <table class="item__info-table">
                    <tr>
                        <th class="info-table__title">カテゴリー</th>
                        <td>
                            @foreach($item->categories as $category)
                                <p class="info-table__category">
                                    {{ $category->content }}@if(!$loop->last)  @endif
                                </p>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th class="info-table__title">商品の状態</th>
                        <td class="info-table__condition">{{ $item->item_condition_text }}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="comment-form">
                <h3 class="item__title">コメント(1)</h3>
                <div class="comment-form__user">
                    <p class="user-icon">◯</p>
                    <p class="user-name">admin</p>
                    {{-- <div class="profile__image" id="imagePreview">
                    @if (!empty($profileImage))
                        <img id="previewImage"
                            src="{{ $profileImage }}"
                            alt="プロフィールアイコン" />
                    @else
                        <div class="profile__image"></div>
                    @endif
                    <div class="profile__username">{{ $user->username }}</div>
                </div> --}}
                </div>
                <p class="user-comment">コメント</p>
                <form action="">
                    @csrf
                    <h4 class="comment-form__text-title">商品へのコメント</h4>
                    <textarea name="" id="" class="comment-form__textarea"></textarea>
                    <button class="comment-form__button">コメントを送信する</button>
                </form>
            </div>
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
