{{-- 商品一覧画面 --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/home.css') }}" />
@endsection

@section('content')
    <div class="home-container">
        <div class="home__header">
            <a href="{{ route('home', ['query' => request('query')]) }}" class="home__tab {{ $tab !== 'mylist' ? 'active' : '' }}">
                <h2>おすすめ</h2>
            </a>
            <a href="{{ route('home', ['tab' => 'mylist', 'query' => request('query')]) }}" class="home__tab {{ $tab === 'mylist' ? 'active' : '' }}">
                <h2>マイリスト</h2>
            </a>
        </div>
        <hr class="home__divider">
    {{-- タブの切替表示 --}}
        <div class="home__main">
            @if ($tab === 'mylist' && auth()->check())
                @if ($items->isEmpty())
                    <p>お気に入り登録した商品がありません</p>
                @endif
            @endif

            @if ($tab !== 'mylist' || auth()->check())
                @foreach ($items as $item)
                    <a href="{{ route('item.detail', ['item_id' => $item->id]) }} " class="item-link">
                        <div class="item-container {{ $item->is_sold ? 'soldout' : '' }}">
                            @if ($item->is_sold)
                                <span class="sold-label visually-hidden">Sold</span> <!-- 非表示にする -->
                            @endif
                            @if ($item->image)
                                <div class="item-image">                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" />
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
@endsection
