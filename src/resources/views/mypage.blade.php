@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
<link rel="stylesheet" href="{{ asset('css/item_list.css') }}">
@endsection

@section('content')
<div class="user-info">
    @if(Auth::user()->profile_pic)
        <img src="{{ Auth::user()->profile_pic_url }}" alt="プロフィール画像" class="user-image">
    @else
        <div class="user-image-placeholder"></div>
    @endif
    <div class="user-details">
        <h1 class="text-xl font-semibold">{{ Auth::user()->username }}</h1>
        <a href="{{ route('address.edit')}}" class="edit-profile-button">プロフィールを編集</a>
    </div>
</div>

<div class="header-container">
    <span class="caption">
        <a href="{{ url('/mypage?tab=sell') }}" class="tab-link {{ $activeTab === 'sell' ? 'active' : '' }}">出品した商品</a>
    </span>
    <span class="caption">
        <a href="{{ url('/mypage?tab=buy') }}" class="tab-link {{ $activeTab === 'buy' ? 'active' : '' }}">購入した商品</a>
    </span>
    <div class="tab-item">
        <a href="{{ route('mypage', ['tab' => 'chat']) }}" class="tab-link @if($activeTab === 'chat') active @endif">
            取引中の商品
        </a>
        @if ($unreadChatCount > 0)
            <span class="unread-badge">{{ $unreadChatCount }}</span>
        @endif
    </div>
</div>
<div class="border-line"></div>

<div class="container">
    @if ($items->isEmpty())
        <div class="no-items-message">商品がありません。</div>
    @else
        <div class="item-grid">
            @foreach ($items as $item)
    <div class="item-card">
        {{-- リンクはカード全体を囲む --}}
        <a href="{{ $activeTab === 'chat' ? route('chat.show', ['item' => $item->id]) : route('item.detail', ['id' => $item->id]) }}">
            
            {{-- ★★★ 画像とバッジだけを囲むコンテナ ★★★ --}}
            <div class="item-image-container">

                {{-- 未読バッジの表示ロジック --}}
                @if ($activeTab === 'chat' && isset($item->unread_messages_count) && $item->unread_messages_count > 0)
                    <div class="unread-item-badge">{{ $item->unread_messages_count }}</div>
                @endif
                
                {{-- "SOLD"の表示 --}}
                @if($item->buyer_id)
                    <div class="sold-overlay">SOLD</div>
                @endif

                {{-- 商品画像 --}}
                <img src="{{ $item->image }}" alt="{{ $item->item_name }}" class="item-image"/>
            
            </div> {{-- ★★★ item-image-container の閉じタグ ★★★ --}}

            {{-- ★★★ 商品名はコンテナの外に出す ★★★ --}}
            <p class="item-title">{{ $item->item_name }}</p>

        </a> {{-- ★★★ aタグの閉じタグ ★★★ --}}
    </div> {{-- ★★★ item-card の閉じタグ ★★★ --}}
@endforeach
        </div>
    @endif
</div>

<div class="pagination">
    {{ $items->links() }}
</div>

@endsection

@section('javascript')

@endsection