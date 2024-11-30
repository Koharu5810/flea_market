{{-- 商品出品画面 --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}" />
@endsection

@section('title', '商品の出品')

@section('content')
    <div class="sell-container">
        <form action="{{ route('sell') }}" method="POST" enctype="multipart/form-data" class="sell-form">
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
            @error('image')
                <p class="sell-form__error">
                    {{ $message }}
                </p>
            @enderror
        {{-- 商品の詳細 --}}
            <h2 class="sell-component">商品の詳細</h2>
            <h3 class="sell-title">カテゴリー</h3>
            <div class="category-container">
                @foreach($categories as $category)
                    <button
                        type="button"
                        class="sell-category {{ in_array($category->id, old('category', [])) ? 'selected' : '' }}"
                        data-category-id="{{ $category->id }}">
                        {{ $category->content }}
                    </button>
                @endforeach
            </div>
            @error('category')
                <p class="sell-form__error">
                    {{ $message }}
                </p>
            @enderror
            <div id="selectedCategoriesContainer"></div>
            <h3 class="sell-title">ブランド名</h3>
            <input type="text" name="brand" value="{{ old('brand') }}" class="sell-brand" />
            @error('brand')
                <p class="sell-form__error">
                    {{ $message }}
                </p>
            @enderror
            <h3 class="sell-title">商品の状態</h3>
                <select name="item_condition" id="" class="sell-select">
                    <option value="" disabled selected>選択してください</option>
                    <option value="1" @selected(old('item_condition') == 1)>良好</option>
                    <option value="2" @selected(old('item_condition') == 2)>目立った傷や汚れなし</option>
                    <option value="3" @selected(old('item_condition') == 3)>やや傷や汚れあり</option>
                    <option value="4" @selected(old('item_condition') == 4)>状態が悪い</option>
                </select>
            @error('item_condition')
                <p class="sell-form__error">
                    {{ $message }}
                </p>
            @enderror
        {{-- 商品名と説明 --}}
            <h2 class="sell-component">商品名と説明</h2>
            <h3 class="sell-title">商品名</h3>
            <input type="text" name="name" value="{{ old('name') }}" class="sell-item-name" />
            @error('name')
                <p class="sell-form__error">
                    {{ $message }}
                </p>
            @enderror
            <h3 class="sell-title">商品の説明</h3>
            <textarea name="description" id="" class="sell-description">{{ old('description') }}</textarea>
            @error('description')
                <p class="sell-form__error">
                    {{ $message }}
                </p>
            @enderror
            <h3 class="sell-title">販売価格</h3>
            <div class="price-container">
                <input type="text" name="price" value="{{ old('price') }}" class="sell-price" />
            </div>
            @error('price')
                <p class="sell-form__error">
                    {{ $message }}
                </p>
            @enderror
        {{-- 出品ボタン --}}
            <button type="submit" class="sell-form__button red-button">出品する</button>
        </form>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('fileInput');
        const imagePreview = document.getElementById('imagePreview');
        const sellImageContainer = document.querySelector('.sell-image');
        const categoryButtons = document.querySelectorAll('.sell-category');
        const selectedCategoriesContainer = document.getElementById('selectedCategoriesContainer');
        const selectedCategories = new Set();
        const oldCategories = @json(old('category', []));

        // ファイル選択時のプレビュー処理
        fileInput?.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    sellImageContainer.style.justifyContent = 'flex-start';
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.src = '';
                imagePreview.style.display = 'none';
                sellImageContainer.style.justifyContent = 'center';
            }
        });

        // 過去選択されたカテゴリーの復元
        categoryButtons.forEach(button => {
            const categoryId = Number(button.dataset.categoryId);
            if (oldCategories.includes(categoryId)) {
                button.classList.add('selected');
                selectedCategories.add(categoryId); // 復元したカテゴリーをセットに追加
            }

            // カテゴリー選択処理
            button.addEventListener('click', function () {
                const categoryId = Number(this.dataset.categoryId);
                if (selectedCategories.has(categoryId)) {
                    selectedCategories.delete(categoryId); // 選択解除
                    this.classList.remove('selected');
                } else {
                    selectedCategories.add(categoryId); // 選択
                    this.classList.add('selected');
                }
                updateHiddenFields();
            });
        });

        // hiddenフィールドの更新
        function updateHiddenFields() {
            selectedCategoriesContainer.innerHTML = ''; // 初期化
            selectedCategories.forEach(categoryId => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'category[]'; // 配列形式で送信
                hiddenInput.value = categoryId;
                selectedCategoriesContainer.appendChild(hiddenInput);
            });
        }

        // 初期状態のhiddenフィールドを設定
        updateHiddenFields();
    });
</script>

@endsection
