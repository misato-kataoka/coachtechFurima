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
                <button type="button" class="upload-button">画像を選択する</button>  
                <input type="file" name="image" id="image" class="file-input" required>  
            </div>  
        </div>  

        <div class="form-group">  
            <label class="label">カテゴリー</label>  
            <div class="categories">  
                @foreach ($categories as $category)  
                    <label class="category-label">  
                        <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" class="category-checkbox">  
                        <span class="category-name">{{ $category->category_name }}</span>  
                    </label>  
                @endforeach  
            </div>
        </div>

        <div class="form-group">  
            <label for="condition" class="label">商品の状態</label>  
            <select name="condition" id="condition" class="select" required>  
                <option value="">選択してください</option>  
                @foreach ($conditions as $condition)  
                    <option value="{{ $condition->id }}">{{ $condition->condition }}</option>  
                @endforeach
            </select>  
        </div>  

        <div class="form-group">  
            <label for="item_name" class="label">商品名</label>  
            <input type="text" name="item_name" id="item_name" class="input" required>  
        </div>  

        <div class="form-group">  
            <label for="brand" class="label">ブランド名</label>  
            <input type="text" name="brand" id="brand" class="input">  
        </div>  

        <div class="form-group">  
            <label for="description" class="label">商品の説明</label>  
            <textarea name="description" id="description" class="textarea" required></textarea>  
        </div>  

        <div class="form-group">  
            <label for="price" class="label">販売価格</label>  
            <input type="number" name="price" id="price" class="input" required>  
        </div>

        <button type="submit" class="submit-button">出品する</button>  
    </form>  
</div>  
@endsection  