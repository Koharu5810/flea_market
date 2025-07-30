{{-- 出品者用評価モーダル --}}

<div id="sellerModal" class="modal hidden">
    <div class="modal-content">
        <h3>取引が完了しました。</h3>

        <hr class="modal-divider">
        <p class="rating-message">今回の取引相手はどうでしたか？</p>

        <form action="{{ route('chat.sellerRate', ['chatRoom' => $chatRoom->id]) }}" method="POST">
            @csrf

            <div class="star-rating">
                @for ($i = 5; $i >= 1; $i--)
                    <input type="radio" name="rating" id="seller-star{{ $i }}" value="{{ $i }}">
                    <label for="seller-star{{ $i }}">★</label>
                @endfor
            </div>

            <hr class="modal-divider">

            <div class="rating-button-container">
                @error('rating')
                    <div class="rating-error-message">
                        {{ $message }}
                    </div>
                @enderror

                <button type="submit" class="rating-submit-button">送信する</button>
            </div>
        </form>
    </div>
</div>

{{-- 購入者評価後、出品者に評価モーダル表示 --}}
@if ($shouldShowRatingModal)
    <script>
        window.addEventListener('DOMContentLoaded', () => openModal('sellerModal'));
    </script>
@endif

{{-- バリデーション時にモーダルを再表示 --}}
@if ($errors->has('buyer_rated') && $isSeller)
    <script>
        window.addEventListener('DOMContentLoaded', () => openModal('sellerModal'));
    </script>
@endif
