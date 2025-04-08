@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item_list.css') }}">
@endsection

@section('content')
<div class="container">
    <h2>おすすめ</h2>
    <h3><a href="/?tab=mylist" class="tab-link">マイリスト</a></h3>

    <div class="product-grid">
        @foreach ($products as $product)
            <div class="product-card">
                <img src="{{ $product->image_url }}" alt="商品画像" class="product-image">
                <h4 class="product-title">{{ $product->name }}</h4>
            </div>
        @endforeach
    </div>

    <div class="pagination">
        {{ $products->links() }} <!-- Laravelのページネーション -->
    </div>
</div>
@endsection