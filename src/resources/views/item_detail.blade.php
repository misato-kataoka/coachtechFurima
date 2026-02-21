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
            <div class="item-brand">ブランド名: {{ $item->brand }}</div>
            <div class="item-price"> ¥{{ number_format($item->price) }}（税込）</div>

        <div class="likes-and-comments">
            <div class="likes">
                <form action="{{ route('item.like', $item->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="like-button">
                    <img src="{{ asset($userLiked ? 'image/star2.png' : 'image/star.png') }}" alt="いいね" class="like-icon">
                    <span class="like-count">{{ $likesCount }}</span>
                    </button>
                </form>
            </div>

            <div class="comments-section">
                <img src="{{ asset('image/comment.png') }}" alt="コメント" class="comment-icon">
                <span class="comment-count">{{ $item->comments->count() }}</span>
            </div>
        </div>

        <form action="{{ route('purchase.show', ['item_id' => $item->id]) }}" method="GET">
            @if ($item->status === 'on_sale')
                <button type="submit" class="buy-button">購入手続きへ</button>
            @else
                <button type="button" class="sold-out-button" disabled>売り切れました</button>
            @endif
        </form>
        <a href="{{ url('/') }}" class="back-button">戻る</a>

        <div class="item-description">
            <h2>商品説明</h2>
            <p>{{ $item->description }}</p>
        </div>

        <div class="product-info">
            <h2>商品の情報</h2>
            <div class="info-group">
                <span class="info-label">カテゴリー</span>
                <div class="category-tags">
                    @forelse ($item->categoryConditions as $categoryCondition)
                        <span class="category-tag">{{ $categoryCondition->category->category_name ?? '未定義' }}</span>
                    @empty
                        <span class="category-tag">未定義</span>
                    @endforelse
                </div>
            </div>
            <div class="info-group">
                <span class="info-label">商品の状態</span>
                <span class="info-value">{{ $item->categoryConditions->first()->condition->condition ?? '未定義' }}</span>
            </div>
        </div>

        <div class="comment-area">
            <h2>コメント ({{ $item->comments->count() }})</h2>

            <div class="comment-list">
                @forelse ($item->comments as $comment)
                    <div class="comment-item">

                        <div class="comment-header">
                            @if ($comment->user->profile_pic_url)
                                {{-- 画像がある場合 --}}
                                <img src="{{ $comment->user->profile_pic_url }}" alt="{{ $comment->user->name }}のアイコン" class="user-icon">
                            @else
                                {{-- 画像がない場合 --}}
                                <div class="user-icon-default"></div>
                            @endif
                            <strong class="username">{{ $comment->user->username }}</strong>
                        </div>

                        <div class="comment-body">
                            <p class="comment-content">{{ $comment->content }}</p>
                        </div>
                    </div>
                @empty
                    <p class="no-comments">まだコメントはありません。</p>
                @endforelse
            </div>

    {{-- コメント投稿フォームのエリア --}}
            <div class="comment-form-section">
                <h3>商品へのコメント</h3>
                <form action="{{ route('comments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                    <textarea name="content" class="comment-textarea" placeholder="コメントを入力して下さい。">{{ old('content') }}</textarea>
                    @error('content')
                        <div class="form__error">{{ $message }}</div>
                    @enderror
                    <button type="submit" class="comment-submit-button">コメントを送信する</button>
                </form>
            </div>
        </div>

</div>
@endsection