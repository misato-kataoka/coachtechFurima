@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item_detail.css') }}">
@endsection

@section('content')
<div class="item-detail-container">
    <h1 class="item-title">{{ $item->item_name }}</h1>
    <div class="product-image">
        <img src="{{ $item->image }}" alt="商品画像" class="item-image">
    </div>
    <div class="item-brand">ブランド: {{ $item->brand }}</div>
    <div class="item-price">価格: ¥{{ number_format($item->price) }}（税込）</div>
    <div class="product-description">
        <h2>商品説明</h2>
        <p>{{ $item->description }}</p>
    </div>

    <div class="product-info">
        <h2>商品情報</h2>
        <ul>
            <li>カテゴリ: {{ $item->category }}</li>
            <li>状態: {{ $item->condition }}</li>
        </ul>
    </div>

    <div class="comments">
        <h2>コメント ({{ $item->comments->count() }})</h2>
        @foreach ($item->comments as $comment)
            <div class="comment">
                <strong>{{ $comment->user->name }}</strong>
                <p>{{ $comment->content }}</p>
            </div>
        @endforeach

        <form action="{{ route('comments.store') }}" method="POST">
            @csrf
            <textarea name="content" placeholder="こちらにコメントが入ります。" required></textarea
            <input type="hidden" name="item_id" value="{{ $item->id }}">
            <button type="submit">コメントを送信する</button>
        </form>
    </div>

    <button class="buy-button">購入手続きへ</button>
</div>

    <a href="{{ url()->previous() }}" class="back-button">戻る</a> <!-- 戻るボタン -->
</div>
@endsection