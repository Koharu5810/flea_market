{{-- 商品出品画面 --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}" />
@endsection

@section('title', '商品の出品')

@section('content')
    <div class="sell-container">
        <form action="" method="POST" class="sell-form">
            @csrf
        {{-- 商品画像 --}}
            <h3 class="sell-title">商品画像</h3>
            <div class="sell-image">
                <label for="fileInput" class="sell-image__button">画像を選択する</label>
                <input type="file" name="image" id="fileInput" class="sell-image__input">
            </div>
        {{-- 商品の詳細 --}}
            <h2 class="sell-component">商品の詳細</h2>
            <h3 class="sell-title">カテゴリー</h3>
            <div>
                @foreach
                
                @endforeach
            </div>
            <h3 class="sell-title">ブランド名</h3>
            <input type="text" class="sell__item-name" />
            <h3 class="sell-title">商品の状態</h3>
            <select name="" id="" class="sell-select">
                <option value="">選択してください</option>
                <option value="">選択肢</option>
            </select>
        {{-- 商品名と説明 --}}
            <h2 class="sell-component">商品名と説明</h2>
            <h3 class="sell-title">商品名</h3>
            <input type="text" class="sell__item-name" />
            <h3 class="sell-title">商品の説明</h3>
            <textarea name="" id="" cols="30" rows="10" class="sell-detail"></textarea>
            <h3 class="sell-title">販売価格</h3>
            <div class="price-container">
                <input type="text" class="sell-price" />
            </div>
        {{-- 出品ボタン --}}
            <button class="sell-form__button">出品する</button>
        </form>
    </div>
@endsection
