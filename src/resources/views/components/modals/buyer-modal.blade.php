{{-- 購入者取引完了モーダル --}}
<div id="buyerModal" class="modal hidden">
    <div class="modal-content">
        <h3>取引が完了しました。</h3>

        <hr class="modal-divider">
        <p class="rating-message">今回の取引相手はどうでしたか？</p>

        <form action="{{ route('chat.buyerRate', ['chatRoom' => $chatRoom->id]) }}" method="POST">
            @csrf

            <div class="star-rating">
                @for ($i = 5; $i >= 1; $i--)
                    <input type="radio" name="rating" id="buyer-star{{ $i }}" value="{{ $i }}">
                    <label for="buyer-star{{ $i }}">★</label>
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

{{-- バリデーション時にモーダルを再表示 --}}
@if ($errors->has('rating') && $isBuyer)
    <script>
        window.addEventListener('DOMContentLoaded', () => openModal('buyerModal'));
    </script>
@endif
