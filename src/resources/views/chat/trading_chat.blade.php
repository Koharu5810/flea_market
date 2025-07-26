{{-- チャット画面 --}}
@extends('layouts.chat')

@section('content')
    <div class="chat-container">

{{-- サイドバー --}}
        <aside class="sidebar">
            <h3>その他の取引</h3>
        </aside>

{{-- 取引相手表示 --}}
        <main class="main-area">
            <section class="user-info">
                <div class="user-meta">
                    @if ($order->user->profile_image_url)
                        <img src="{{ $order->user->profile_image_url }}" alt="アイコン" class="user-icon" />
                    @else
                        <div class="default-icon"></div>
                    @endif
                    <span class="user-name">{{ $order->user->username }}さんとの取引画面</span>
                </div>

                <button class="complete-button">取引を完了する</button>
            </section>

            <hr class="section-divider">

    {{-- 取引商品表示 --}}
            <section class="item-area">
                <div class="item-image-container">
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="item-image" />
                </div>
                <div class="item-info">
                    <h3 class="item-name">{{ $item->name }}</h3>
                    <p class="item-price">&yen;<span>{{ number_format($item->price) }}</span> (税込)</p>
                </div>
            </section>

            <hr class="section-divider">

    {{-- チャット欄 --}}
            <section class="chat-area">
                @foreach($messages as $message)
                    @php
                        $isMine = $message->sender_id === auth()->id();
                        $sender = $message->sender;
                    @endphp

                    <div class="chat-message {{ $isMine ? 'mine' : 'theirs' }}">
            {{-- アイコン --}}
                        @if ($sender->profile_image_url)
                            <img src="{{ $sender->profile_image_url }}" alt="アイコン" class="message-avatar" />
                        @else
                            <div class="message-avatar default-icon"></div>
                        @endif

                        <div class="message-content">
            {{-- ユーザー名 --}}
                            <span class="user-name">{{ $sender->username }}</span>

            {{-- メッセージ内容 --}}
                            <div class="message-bubble">{{ $message->content }}</div>
                        </div>
                    </div>
                @endforeach

        {{-- メッセージ送信フォーム --}}
                {{-- <form action="{{ route('comments.store', ['item_id' => $item->id]) }}" method="POST"> --}}
                <form action="" method="POST" class="message-input-area">
                    @csrf
                    <input
                        type="text"
                        name="query"
                        value="{{ request('query') }}"
                        placeholder="取引メッセージを記入してください"
                        class="message-input"
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
        </main>
    </div>
@endsection
