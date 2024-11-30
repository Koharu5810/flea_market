{{-- 商品購入ページ --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/index.css') }}" />
@endsection

@section('content')
    @if (session('error'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

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
            <hr class="page-line">
            <form method="POST" action="{{ route('purchase.checkout', ['item_id' => $item->id]) }}">
                @csrf

        {{-- 支払い方法 --}}
                <h3 class="parts-title">支払い方法</h3>
                <select name="payment_method" id="payment_method" class="pay-select">
                    <option value="" disabled selected>選択してください</option>
                    <option value="コンビニ支払い">コンビニ支払い</option>
                    <option value="カード支払い">カード支払い</option>
                </select>
                <hr class="page-line">
            {{-- 配送先 --}}
                <div class="delivery-container">
                    <h3 class="parts-title">配送先</h3>
                    <a href="{{ route('show.purchase.address', ['item_id' => $item->id]) }}" class="change-address-button blue-button">変更する</a>
                </div>
                <div class="delivery-detail">
                    <p>〒 {{ $address->postal_code }}</p>
                    <p>{{ $address->address }} {{ $address->building }}</p>
                </div>
                <hr class="page-line">
            </div>
        {{-- 右側 --}}
            <div class="right-container">
                    <table class="confirmation-table">
                        <tr>
                            <th>商品代金</th>
                            <td>&yen;<span>{{ number_format($item->price) }}</span></td>
                        </tr>
                        <tr>
                            <th>支払い方法</th>
                            <td id="payment_method_show">選択してください</td>
                        </tr>
                    </table>
                <button type="submit" class="purchase-button red-button">購入する</button>
                <input type="hidden" name="item_id" value="{{ $item->id }}">
            </form>
        </div>
    </div>

{{-- 支払い方法をconfirmation-tableに即時反映する --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
        const selectElement = document.querySelector("#payment_method");
        const paymentMethodCell = document.querySelector("#payment_method_show");

            // selectの値が変化したときに実行
            selectElement.addEventListener("change", () => {
                // 選択された値を取得
                const selectedValue = selectElement.value;

                // 支払い方法のセル内容を更新
                paymentMethodCell.textContent = selectedValue || "選択してください";
            });
        });
    </script>
@endsection
