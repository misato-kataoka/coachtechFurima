@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="item-title">{{ $item->item_name }}</h1>
    <img src="{{ $item->image }}" alt="商品画像" class="item-image">
    <p class="item-brand">ブランド: {{ $item->brand }}</p>
    <p class="item-price">価格: ¥{{ number_format($item->price) }}</p>
    <p class="item-description">{{ $item->description }}</p>

    <a href="{{ url()->previous() }}" class="back-button">戻る</a> <!-- 戻るボタン -->
</div>
@endsection