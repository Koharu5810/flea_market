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
                <span class="trade-with-name">{{ $order->user->username }}さんとの取引画面</span>
            </div>

            @if ($isBuyer)
                <button class="complete-button">取引を完了する</button>
            @endif
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
                    <div class="message-meta">
            {{-- アイコン --}}
                        @if ($sender->profile_image_url)
                            <img src="{{ $sender->profile_image_url }}" alt="アイコン" class="message-avatar" />
                        @else
                            <div class="message-avatar default-icon"></div>
                        @endif

            {{-- ユーザー名 --}}
                        <span class="user-name">{{ $sender->username }}</span>
                    </div>

            {{-- 自分のチャットには更新・更新キャンセルボタンを表示（編集時） --}}
                    @if (session('edit_id') === $message->id)

                        <form action="{{ route('chat.update', ['chatRoom' => $chatRoom->id, 'message' => $message->id]) }}" method="POST" class="edit-form">
                            @csrf
                            @method('PUT')
                            <input type="text" name="content" value="{{ old('content', $message->content) }}" class="message-edit-input">

                            <div class="edit-buttons">
                                <button type="submit" class="edit-button">更新</button>
                                <button type="button" class="cancel-button" onclick="cancelEdit()">キャンセル</button>
                            </div>
                        </form>

                    @else
                        <div class="message-bubble">{{ $message->content }}</div>
                        @if ($message->image_path)
                            <img src="{{ asset('storage/' . $message->image_path) }}" alt="画像" class="chat-image" />
                        @endif
                    @endif

            {{-- 自分のチャットには編集・削除ボタンを表示 --}}
                    @if ($isMine && session('edit_id') !== $message->id)
                        <div class="message-actions">
                            <a href="{{ route('chat.edit', ['chatRoom' => $chatRoom->id, 'message' => $message->id]) }}" class="edit-button">編集</a>
                            <form action="{{ route('chat.delete', ['chatRoom' => $chatRoom->id, 'message' => $message->id]) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-button" onclick="return confirm('削除してもよろしいですか？')">削除</button>
                            </form>
                        </div>
                    @endif

                </div>
            @endforeach

    {{-- メッセージ送信フォーム --}}
            <div>
                @error('content')
                    <span class="error-message">
                        {{ $message }}
                    </span>
                @enderror
                @error('image_path')
                    <span class="error-message">
                        {{ $message }}
                    </span>
                @enderror

                <form action="{{ route('chat.send', ['chatRoom' => $chatRoom->id]) }}" method="POST" enctype="multipart/form-data" class="message-input-area">
                    @csrf
                    <input
                        type="text"
                        name="content"
                        id="chatInput"
                        value="{{ old('content') }}"
                        placeholder="取引メッセージを記入してください"
                        class="message-input"
                    />

                    <div class="sell-image">
                        <div class="image-preview-container">
                            <img
                                id="imagePreview"
                                alt="プレビュー画像"
                                class="image-preview"
                                style="display: none;"
                            />
                        </div>
                        <label for="fileInput" class="edit__red-button">画像を追加</label>
                        <input type="file" name="image" id="fileInput" class="sell-image__input">
                    </div>

                    <button type="submit"  class="message-send-button">
                        <img src="{{ asset('images/app/input-button.jpg') }}" alt="送信" />
                    </button>
                </form>
            </div>

        </div>
    </main>
</div>

<script>
// 更新キャンセルボタン
    function cancelEdit() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('chat.cancelEdit', ['chatRoom' => $chatRoom->id]) }}";

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';

        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }

// チャット保持
    const chatInput = document.getElementById('chatInput');
    const storageKey = 'chat_draft_content';

    // 画面読み込み時：保存されている内容があれば復元
    document.addEventListener('DOMContentLoaded', () => {
        const saved = localStorage.getItem(storageKey);
        if (saved) {
            chatInput.value = saved;
        }
    });

    // 入力時に保存
    chatInput.addEventListener('input', () => {
        localStorage.setItem(storageKey, chatInput.value);
    });

    // フォーム送信時：保存内容をクリア
    const form = chatInput.closest('form');
    form.addEventListener('submit', () => {
        localStorage.removeItem(storageKey);
    });
</script>

@endsection
