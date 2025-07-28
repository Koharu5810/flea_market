{{-- チャット画面 --}}
@extends('layouts.chat')

@section('content')

<div class="chat-container">

{{-- サイドバー --}}
    <aside class="sidebar">
        <h3>その他の取引</h3>

        @if ($otherOrders->isNotEmpty())
            @foreach ($otherOrders as $other)
                <div class="sidebar__other-order">
                    <a href="{{ route('chat.show', ['chatRoom' => $other->chatRoom->id]) }}">
                        {{ $other->item->name ?? '取引中商品なし' }} {{-- エラー対応 --}}
                    </a>
                </div>
            @endforeach
        @endif
    </aside>

{{-- 取引相手表示 --}}
    <div class="main-area">
        <section class="user-info">
            <div class="user-meta">
                @if ($order->user->profile_image_url)
                    <img src="{{ $order->user->profile_image_url }}" alt="アイコン" class="user-icon" />
                @else
                    <div class="default-icon"></div>
                @endif
                <span class="trade-with-name">{{ $order->user->username }}さんとの取引画面</span>
            </div>

    {{-- 購入者取引完了モーダル --}}
            @if ($isBuyer && $order->status !== 'complete')
                {{-- ボタン --}}
                <button class="buyer-rate-button" onclick="openBuyerModal()">取引を完了する</button>

                {{-- モーダル --}}
                <div id="buyerModal" class="modal hidden">
                    <div class="modal-content">
                        <p>取引が完了しました。</p>

                        <hr class="modal-divider">

                        <p>今回の取引相手はどうでしたか？</p>
                        <form action="{{ route('chat.buyerRate', ['chatRoom' => $chatRoom->id]) }}" method="POST">
                            @csrf

                            <div class="star-rating">
                                @for ($i = 5; $i >= 1; $i--)
                                    <input type="radio" name="rating" id="buyer-star{{ $i }}" value="{{ $i }}">
                                    <label for="buyer-star{{ $i }}">★</label>
                                @endfor
                            </div>

                            @error('rating')
                                <div class="error-message">
                                    {{ $message }}
                                </div>
                            @enderror

                        {{-- バリデーション時にモーダルを再表示 --}}
                            @if ($errors->has('rating') && $isBuyer)
                                <script>
                                    window.addEventListener('DOMContentLoaded', function () {
                                        document.getElementById('buyerModal')?.classList.remove('hidden');
                                    });
                                </script>
                            @endif

                            <hr class="modal-divider">

                            <button type="submit">送信する</button>
                        </form>
                    </div>
                </div>
            @endif

    {{-- 購入者評価後、出品者に評価モーダル表示 --}}
            @if ($shouldShowRatingModal)
                <script>
                    window.addEventListener('DOMContentLoaded', function () {
                        openSellerModal();
                    });
                </script>
            @endif

    {{-- 出品者用評価モーダル --}}
            <div id="sellerModal" class="modal hidden">
                <div class="modal-content">
                    <p>取引が完了しました。</p>

                    <hr class="modal-divider">

                    <p>今回の取引相手はどうでしたか？</p>
                    <form action="{{ route('chat.sellerRate', ['chatRoom' => $chatRoom->id]) }}" method="POST">
                        @csrf

                        <div class="star-rating">
                            @for ($i = 5; $i >= 1; $i--)
                                <input type="radio" name="rating" id="seller-star{{ $i }}" value="{{ $i }}">
                                <label for="seller-star{{ $i }}">★</label>
                            @endfor
                        </div>

                        @error('rating')
                            <div class="error-message">
                                {{ $message }}
                            </div>
                        @enderror

                    {{-- バリデーション時にモーダルを再表示 --}}
                        @if ($errors->has('buyer_rated') && $isSeller)
                            <script>
                                window.addEventListener('DOMContentLoaded', function () {
                                    document.getElementById('sellerModal')?.classList.remove('hidden');
                                });
                            </script>
                        @endif

                        <hr class="modal-divider">

                        <button type="submit">送信する</button>
                    </form>
                </div>
            </div>

        </section>

        <hr class="section-divider">

        <div class="scrollable-content">
{{-- 取引商品表示 --}}
            <section class="item-area">
                <div class="item-image-container">
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" />
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
                        <div class="message-meta" {{ $isMine ? 'mine' : 'theirs' }}>

                    {{-- 自分：名前 → アイコン --}}
                            @if ($isMine)
                                <span class="sender-name">{{ $sender->username }}</span>

                                @if ($sender->profile_image_url)
                                    <img src="{{ $sender->profile_image_url }}" alt="アイコン" class="message-avatar" />
                                @else
                                    <div class="message-icon default-icon"></div>
                                @endif

                    {{-- 相手：アイコン → 名前 --}}
                            @else
                                @if ($sender->profile_image_url)
                                    <img src="{{ $sender->profile_image_url }}" alt="アイコン" class="message-avatar" />
                                @else
                                    <div class="message-icon default-icon"></div>
                                @endif

                                <span class="sender-name">{{ $sender->username }}</span>
                            @endif

                        </div>

                {{-- 自分のチャットには更新・更新キャンセルボタンを表示（編集時） --}}
                        @if (session('edit_id') === $message->id)

                            <form action="{{ route('chat.update', ['chatRoom' => $chatRoom->id, 'message' => $message->id]) }}" method="POST" class="edit-form">
                                @csrf
                                @method('PUT')
                                <textarea name="content" id="" oninput="autoResize(this)" class="message-edit-textarea">{{ old('content', $message->content) }}</textarea>

                                <div class="update-buttons">
                                    <button type="submit" class="update-button">更新</button>
                                    <button type="button" class="cancel-button" onclick="cancelEdit()">キャンセル</button>
                                </div>
                            </form>

                        @else
                            <div class="message-bubble">{!! nl2br(e(rtrim($message->content))) !!}</div>
                            @if ($message->image_path)
                                <img src="{{ asset('storage/' . $message->image_path) }}" alt="画像" class="chat-image" />
                            @endif
                        @endif

                {{-- 自分のチャットには編集・削除ボタンを表示 --}}
                        @if ($isMine && session('edit_id') !== $message->id)
                            <div class="edit-buttons">
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
            </section>
        </div>

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
                <div class="textarea-with-preview">
                    <div class="image-preview-wrapper" id="previewWrapper">
                        <img
                            id="imagePreview"
                            alt="プレビュー画像"
                            class="preview-image"
                        />
                        <button type="button" id="removeImageBtn" class="remove-image-button">✕</button>
                    </div>

                    <textarea
                        name="content"
                        id="chatInput"
                        placeholder="取引メッセージを記入してください"
                        class="message-textarea"
                    >{{ old('content') }}</textarea>
                </div>

                <label for="fileInput" class="send-button">画像を追加</label>
                <input type="file" name="image" id="fileInput" class="chat-image__input">

                <button type="submit"  class="message-send-button">
                    <img src="{{ asset('images/app/input-button.jpg') }}" alt="送信" />
                </button>
            </form>
        </div>
    </div>

</div>

<script>
// 送信済みチャット更新キャンセルボタン
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
// 編集チャットの文字数に合わせてtextarea高さを変える
    function autoResize(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 300) + 'px';

        // 横幅自動調整（文字数に応じて幅を変更）
        const dummy = document.createElement('span');
        dummy.style.visibility = 'hidden';
        dummy.style.whiteSpace = 'pre';
        dummy.style.font = getComputedStyle(textarea).font;
        dummy.textContent = textarea.value || textarea.placeholder || '';
        document.body.appendChild(dummy);

        const padding = 32; // paddingの考慮（左右合計）
        const minWidth = 200;
        const maxWidth = textarea.closest('.edit-form')?.clientWidth || 600;
        const contentWidth = dummy.offsetWidth + padding;
        const appliedWidth = Math.min(Math.max(contentWidth, minWidth), maxWidth);

        textarea.style.width = appliedWidth + 'px';
        document.body.removeChild(dummy);
        }
        // 編集フォーム読み込み時にも反映させる
        document.addEventListener('DOMContentLoaded', () => {
        const textarea = document.querySelector('.message-edit-textarea');
        if (textarea) {
            autoResize(textarea);
            textarea.addEventListener('input', () => autoResize(textarea));
        }
    });

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

// 画像プレビュー
    document.addEventListener('DOMContentLoaded', () => {
        const fileInput = document.getElementById('fileInput');
        const imagePreview = document.getElementById('imagePreview');
        const removeBtn = document.getElementById('removeImageBtn');
        const previewWrapper = document.getElementById('previewWrapper');

        fileInput.addEventListener('change', () => {
            const file = fileInput.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => {
                    imagePreview.src = e.target.result;
                    previewWrapper.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        removeBtn.addEventListener('click', () => {
            fileInput.value = '';
            imagePreview.src = '';
            previewWrapper.style.display = 'none';
        });
    });

// 購入者・評価モーダル開閉処理
    function openBuyerModal() {
        document.getElementById('buyerModal')?.classList.remove('hidden');
    }
    function closeBuyerModal() {
        document.getElementById('buyerModal')?.classList.add('hidden');
    }
// 出品者モーダル表示処理
    function openSellerModal() {
        document.getElementById('sellerModal')?.classList.remove('hidden');
    }
    function closeSellerModal() {
        document.getElementById('sellerModal')?.classList.add('hidden');
    }
</script>

@endsection
