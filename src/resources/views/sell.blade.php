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
                <div class="image-preview-container">
                    <img id="imagePreview" alt="プレビュー画像" class="image-preview" style="display: none;" />
                </div>
                <label for="fileInput" class="sell-image__button">画像を選択する</label>
                <input type="file" name="image" id="fileInput" class="sell-image__input">
            </div>
        {{-- 商品の詳細 --}}
            <h2 class="sell-component">商品の詳細</h2>
            <h3 class="sell-title">カテゴリー</h3>
            <div class="category-container">
                @foreach($categories as $category)
                    <button type="button" class="sell-category" data-category-id="{{ $category->id }}">
                        {{ $category->content }}
                    </button>
                @endforeach
            </div>
            {{-- <input type="hidden" name="selected_category" id="selectedCategory" /> --}}
            <div id="selectedCategoriesContainer"></div>
            <h3 class="sell-title">ブランド名</h3>
            <input type="text" class="sell-brand" />
            <h3 class="sell-title">商品の状態</h3>
            <select name="" id="" class="sell-select">
                <option value="">選択してください</option>
                <option value="1">良好</option>
                <option value="2">目立った傷や汚れなし</option>
                <option value="3">やや傷や汚れあり</option>
                <option value="4">状態が悪い</option>
            </select>
        {{-- 商品名と説明 --}}
            <h2 class="sell-component">商品名と説明</h2>
            <h3 class="sell-title">商品名</h3>
            <input type="text" class="sell-item-name" />
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('fileInput');
        const imagePreview = document.getElementById('imagePreview');
        const sellImageContainer = document.querySelector('.sell-image');

        fileInput.addEventListener('change', function () {
            const file = this.files[0]; // 選択されたファイルを取得

            if (file) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    imagePreview.src = e.target.result; // プレビューに画像を表示
                    imagePreview.style.display = 'block'; // プレビューを表示
                    sellImageContainer.style.justifyContent = 'flex-start'; // プレビューがある場合は上揃え
                };

                reader.readAsDataURL(file); // ファイルを読み込む
            } else {
                imagePreview.src = '';
                imagePreview.style.display = 'none'; // プレビューを非表示
                sellImageContainer.style.justifyContent = 'center'; // プレビューがない場合は中央揃え
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const categoryButtons = document.querySelectorAll('.sell-category');
        const selectedCategoriesContainer = document.getElementById('selectedCategoriesContainer');
        const selectedCategories = new Set(); // 選択されたカテゴリーIDを保持

        categoryButtons.forEach(button => {
            button.addEventListener('click', function () {
                const categoryId = this.dataset.categoryId;

                // 選択状態をトグル
                if (selectedCategories.has(categoryId)) {
                    selectedCategories.delete(categoryId);
                    this.classList.remove('selected');
                } else {
                    selectedCategories.add(categoryId);
                    this.classList.add('selected');
                }

                // hiddenフィールドを更新
                updateHiddenFields();
            });
        });

        function updateHiddenFields() {
            // 現在のhiddenフィールドをクリア
            selectedCategoriesContainer.innerHTML = '';

            // 選択されたカテゴリーIDをhiddenフィールドとして追加
            selectedCategories.forEach(categoryId => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'selected_categories[]'; // 配列形式で送信
                hiddenInput.value = categoryId;
                selectedCategoriesContainer.appendChild(hiddenInput);
            });
        }
    });
</script>

@endsection
