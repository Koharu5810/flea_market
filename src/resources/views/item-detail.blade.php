{{-- 商品詳細画面 --}}
@extends('layouts.app')

@section('css')
{{-- ファイル名変更 --}}
<link rel="stylesheet" href="{{ asset('css/xxx.css') }}" />
@endsection

@section('content')
    商品詳細画面


<div class="item-container">
    <div class="item-image">
        @if ($item->image)
            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" />
        @else
            <div class="item-image">商品画像</div>
        @endif
    </div>
    <div class="item-name">
        {{ $item->name }}
    </div>
    <div class="item-name">
        {{ $item->name }}
    </div>
</div>
@endsection
