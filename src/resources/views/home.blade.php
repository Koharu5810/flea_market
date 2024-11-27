{{-- 商品一覧画面 --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/home.css') }}" />
@endsection

@section('content')
    <div class="home-container">
        <div class="home__header">
            <a href="{{ route('home') }}" class="home__tab {{ $tab === 'mylist' ? 'active' : '' }}">
                <h2>おすすめ</h2>
            </a>
            <a href="{{ route('home', ['tab' => 'mylist']) }}" class="home__tab {{ $tab === 'mylist' ? 'active' : '' }}">
                <h2>マイリスト</h2>
            </a>
        </div>
        <hr class="home__divider">
        <div class="home__main">
            @if ($items->isEmpty())
                <p>お気に入り登録した商品がありません</p>
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
@endsection
