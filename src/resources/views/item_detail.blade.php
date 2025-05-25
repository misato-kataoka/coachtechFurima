@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item_detail.css') }}">
@endsection

@section('content')
<div class="item-detail-container">
    <div class="item-card">
        <div class="item-image-container">
            <img src="{{ $item->image }}" alt="商品画像" class="item-image">
        </div>

        <div class="item-info-container">
            <h1 class="item-title">{{ $item->item_name }}</h1>
            <div class="item-brand">ブランド: {{ $item->brand }}</div>
            <div class="item-price">価格: ¥{{ number_format($item->price) }}（税込）</div>

        <div class="likes-and-comments">
            <div class="likes">
                <form action="{{ route('item.like', $item->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="like-button">
                    <img src="{{ asset($userLiked ? 'image/star2.png' : 'image/star.png') }}" alt="いいね" class="like-icon">
                    <span class="like-count">{{ $likesCount }}</span> <!-- いいねの数 -->
                    </button>
                </form>
            </div>

            <div class="comments-section">
                <img src="{{ asset('image/comment.png') }}" alt="コメント" class="comment-icon">
                <span class="comment-count">{{ $item->comments->count() }}</span> <!-- コメントの数 -->
            </div>
        </div>

        <form action="{{ url('/purchase/' . $item->id) }}" method="GET">
            <button type="submit" class="buy-button">購入手続きへ</button>
        </form>
        <a href="{{ url('/') }}" class="back-button">戻る</a> <!-- 戻るボタン -->

        <div class="item-description">
            <h2>商品説明</h2>
            <p>{{ $item->description }}</p>
        </div>

        <div class="product-info">
            <h2>商品情報</h2>
            <ul>
                @if ($item->categoryConditions->isNotEmpty())
                    <li>カテゴリ:</li>
                    <ul>
                        @foreach ($item->categoryConditions as $categoryCondition)
                            <li>{{ $categoryCondition->category->category_name ?? '未定義' }}</li>
                        @endforeach
                    </ul>
                    <li>状態: {{ $item->categoryConditions->first()->condition->condition ?? '未定義' }}</li>
                @else
                    <li>カテゴリ: 未定義</li>
                    <li>状態: 未定義</li>
                @endif
            </ul>
        </div>

        <div class="comments">
            <h2>コメント ({{ $item->comments->count() }})</h2>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @foreach ($item->comments as $comment)
                <div class="comment">
                    <img src="{{ $comment->user->profile_image_url }}" alt="{{ $comment->user->name }}のアイコン" class="user-icon">
                    <strong class="username">{{ $comment->user->name }}</strong>
                    <p class="comment-content">{{ $comment->content }}</p>
                </div>
            @endforeach

            <form action="{{ route('comments.store') }}" method="POST">
                @csrf
                <textarea name="content" placeholder="こちらにコメントが入ります。"></textarea>
                <input type="hidden" name="item_id" value="{{ $item->id }}">
                <button type="submit" class="comment-submit-button">コメントを送信する</button>
            </form>
        </div>
        <div class="form__error">
            @error('content')
                {{ $message }}
            @enderror
        </div>

        </div>

</div>
@endsection