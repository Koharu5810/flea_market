{{-- 商品詳細画面 --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item_detail.css') }}" />
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="item-container">
    {{-- 左側 --}}
        <div class="left-container">
            <div class="item-image">
                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" />
            </div>
        </div>
    {{-- 右側 --}}
        <div class="right-container">
            <div class="item-detail">
                <h2 class="item-name">{{ $item->name }}</h2>
                <p class="item-brand">{{ $item->brand }}</p>
                <p class="item-price">&yen;<span>{{ number_format($item->price) }}</span> (税込)</p>
        {{-- お気に入り・コメントアイコン --}}
                <div class="item-status">
                    <div class="favorite-icon">
                        <form
                            action="{{ auth()->check() ? route('item.favorite', ['item_id' => $item->id]) : '#' }}"
                            method="POST"
                            class="favorite-form"
                            @if (!auth()->check())
                                onclick="return false;"
                            @endif
                        >
                            @csrf
                            <button type="submit" id="favorite-button">
                                <img
                                    src="{{ $item->isFavoriteBy(auth()->user()) ? asset('storage/app/favorited.png') : asset('storage/app/favorite.png') }}"
                                    alt="{{ $item->isFavoriteBy(auth()->user()) ?  'お気に入り登録済み' : 'お気に入り' }}"
                                />
                            </button>
                            <p id="favorite-count">{{ $item->favoriteBy->count() }}</p>
                        </form>
                    </div>
                    <div class="comment-icon">
                        <img src="{{ asset('storage/app/comment.png') }}" alt="コメント" />
                        <p>{{ $item->comments->count() }}</p>
                    </div>
                </div>
            </div>
            @if (!$item->is_sold)
                <form action="{{ route('purchase.show', ['item_id' => $item->id]) }}" method="GET">
                    <button type="submit" class="purchase-button form__red-button">購入手続きへ</button>
                </form>
            @else
                <div class="purchase-button form__red-button" style="pointer-events: none;">Sold Out</div>
            @endif
    {{-- 商品説明・商品の情報 --}}
            <div class="item-description">
                <h3 class="item__title">商品説明</h3>
                {!! nl2br(e($item->description)) !!}
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
    {{-- コメントフォーム --}}
            <div class="comment-form">
                <h3 class="item__title">コメント({{ $item->comments->count() }})</h3>
                @foreach($item->comments as $comment)
                    <div class="comment-form__list">
                        <div class="user-container">
                            @if ($comment->user->profile_image_url)
                                <img src="{{ $comment->user->profile_image_url }}" alt="アイコン" class="user-icon" />
                            @else
                                <div class="default-icon"></div>
                            @endif
                            <p class="user-name">{{ $comment->user->username }}</p>
                        </div>
                        <p class="user-comment">{!! nl2br(e($comment->comment)) !!}</p>
                    </div>
                @endforeach
                <h4 class="comment-form__text-title">商品へのコメント</h4>
                <form action="{{ route('comments.store', ['item_id' => $item->id]) }}" method="POST">
                    @csrf
                    <textarea name="comment" id="comment" class="comment-form__textarea">{{ old('comment') }}</textarea>
                    @if ($errors->any())
                        <div class="error-message">
                            @foreach ($errors->all() as $error)
                                {{ $error }}
                            @endforeach
                        </div>
                    @endif
                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                    <button type="submit" class="comment-form__button form__red-button">コメントを送信する</button>
                </form>
            </div>
        </div>
    </div>
@endsection
