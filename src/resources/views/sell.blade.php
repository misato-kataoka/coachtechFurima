@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="container">
    <h1 class="title">商品の出品</h1>
    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" class="sell-form">
        @csrf

        <div class="form-group">
            <label for="image" class="label">商品画像</label>
            <div class="image-upload">
                <div class="image-container">
                    <label for="image" class="upload-button">画像を選択する</label>
                    <input type="file" name="image" id="image" class="file-input" accept="image/*" onchange="previewImage(event)">
                </div>
                <img id="preview" class="preview-image" alt="画像のプレビュー" style="display:none;">
            </div>
            <div class="form__error">
                @error('image')
                    {{ $message }}
                @enderror
            </div>
        </div>

        <h2 class="subtitle">商品の詳細</h2>

        <div class="form-group">
            <label class="label">カテゴリー</label>
            <div class="categories">
                @foreach ($categories as $category)
                    <label class="category-label">
                        <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" class="category-checkbox"
                            {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                        <span class="category-name">{{ $category->category_name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        <div class="form__error">
            @error('category_ids')
                {{ $message }}
            @enderror
        </div>

        <div class="form-group">
            <label for="condition" class="label">商品の状態</label>
            <select name="condition_id" id="condition" class="select">
                <option value="">選択してください</option>
                @foreach ($conditions as $condition)
                    <option value="{{ $condition->id }}" {{ old('condition_id') == $condition->id ? 'selected' : '' }}>
                        {{ $condition->condition }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form__error">
                @error('condition_id')
                {{ $message }}
                @enderror
        </div>

    <h2 class="subtitle">商品名と説明</h2>

        <div class="form-group">
            <label for="item_name" class="label">商品名</label>
            <input type="text" name="item_name" id="item_name" value="{{ old('item_name') }}" class="input">
        </div>
        <div class="form__error">
            @error('item_name')
                {{ $message }}
            @enderror
        </div>

        <div class="form-group">
            <label for="brand" class="label">ブランド名</label>
            <input type="text" name="brand" id="brand" value="{{ old('brand') }}" class="input">
        </div>

        <div class="form-group">
            <label for="description" class="label">商品の説明</label>
            <textarea name="description" id="description" class="textarea">{{ old('description') }}</textarea>
        </div>
        <div class="form__error">
            @error('description')
                {{ $message }}
            @enderror
        </div>

        <div class="form-group">
            <label for="price" class="label">販売価格</label>
            <input type="number" name="price" id="price" value="{{ old('price') }}" class="input">
        </div>
        <div class="form__error">
            @error('price')
                {{ $message }}
            @enderror
        </div>

        <button type="submit" class="submit-button">出品する</button>
    </form>

    <script>  
        function previewImage(event) {  
            const preview = document.getElementById('preview');  
            preview.style.display = 'block';  
            preview.src = URL.createObjectURL(event.target.files[0]);  
        }  
    </script>

</div>
@endsection