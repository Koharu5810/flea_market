{{-- 商品購入ページ --}}
@extends('layouts.app')

@section('css')
{{-- ファイル名変更 --}}
<link rel="stylesheet" href="{{ asset('css/purchase/index.css') }}" />
@endsection

@section('content')
    <div class="item-container">
    {{-- 左側 --}}
        <div class="left-container">
        {{-- 商品情報 --}}
            <div class="detail-container">
                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="item-image" />
                <div class="item-detail">
                    <h2 class="item-name">{{ $item->name }}</h2>
                    <p class="item-price">&yen;<span>{{ number_format($item->price) }}</span></p>
                </div>
            </div>
            <hr>
        {{-- 支払い方法 --}}
            <h3>支払い方法</h3>
            <select name="select" id="select" class="select">
                <option value="">選択してください</option>
                <option value="">コンビニ支払い</option>
                <option value="">カード支払い</option>
            </select>
            <hr>
        {{-- 配送先 --}}
            <div class="delivery">
                <h3>配送先</h3>
                <form method="GET" action="{{ route('show.purchase.address', ['item_id' => $item->id]) }}">
                    @csrf
                    <button type="submit" class="change-address-button">変更する</button>
                </form>
            </div>
            <div>
                <p>〒 {{ $address->postal_code }}</p>
                <p>{{ $address->address }} {{ $address->building }}</p>
            </div>
        </div>
    {{-- 右側 --}}
        <div class="right-container">
            <table>
                <tr>
                    <th>商品代金</th>
                    <td>&yen;<span>{{ number_format($item->price) }}</span></td>
                </tr>
                <tr>
                    <th>支払い方法</th>
                    <td></td>
                </tr>
            </table>
            {{-- <form method="post" action=""> --}}
                {{-- @csrf --}}
                <button type="submit" class="purchase-button button">購入する</button>
            {{-- </form> --}}
        </div>
    </div>
@endsection
