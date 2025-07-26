{{-- チャット画面 --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat/trading_chat.css') }}" />
@endsection

@section('content')
    <div class="chat-container">

{{-- 取引相手表示 --}}
        <div class="chat__header">
            @if ($order->user->profile_image_url)
                <img src="{{ $order->user->profile_image_url }}" alt="アイコン" class="user-icon" />
            @else
                <div class="default-icon"></div>
            @endif
            <h2 class="user-name">{{ $order->user->username }}さんとの取引画面</h2>

            <button class="form__red-button">取引を完了する</button>
        </div>

        <hr>

{{-- 取引商品表示 --}}
        <div class="chat__item-detail">
            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="item-image" />
            <div class="item-detail-container">
                <h2 class="item-name">{{ $item->name }}</h2>
                <p class="item-price">&yen;<span>{{ number_format($item->price) }}</span> (税込)</p>
            </div>
        </div>

        <hr>

{{-- チャット欄 --}}
        <div class="chat__message">
            @foreach($messages as $message)
                @if ($order->user->profile_image_url)
                    <img src="{{ $order->user->profile_image_url }}" alt="アイコン" class="user-icon" />
                @else
                    <div class="default-icon"></div>
                @endif
                <span class="user-name">{{ $order->user->username }}</span>

                <p>{{ $message->content }}</p>
            @endforeach

            <form action="{{ route('search') }}" method="GET" class="">
                <input
                    type="text"
                    name="query"
                    value="{{ request('query') }}"
                    placeholder="取引メッセージを記入してください"
                    class=""
                />

                <div class="sell-image">
                    <div class="image-preview-container">
                        <img id="imagePreview" alt="プレビュー画像" class="image-preview" style="display: none;" />
                    </div>
                    <label for="fileInput" class="edit__red-button">画像を追加</label>
                    <input type="file" name="image" id="fileInput" class="sell-image__input">
                </div>
                @error('image')
                    <p class="error-message">
                        {{ $message }}
                    </p>
                @enderror

                <img src="{{ asset('images/app/input-button.jpg') }}" alt="送信" class="message-send-button" />
            </form>

        </div>
    </div>
@endsection
