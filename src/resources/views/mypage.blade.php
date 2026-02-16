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
    <span class="caption">
        <a href="{{ url('/mypage?tab=chat') }}" class="tab-link {{ $activeTab === 'chat' ? 'active' : '' }}">取引中の商品</a>
    </span>
</div>
<div class="border-line"></div>

<div class="container">
    @if ($items->isEmpty())
        <div class="no-items-message">商品がありません。</div>
    @else
        <div class="item-grid">
            @foreach ($items as $item)
                <div class="item-card">
                    @if($activeTab === 'chat')
                        <a href="{{ route('chat.show', ['item' => $item->id]) }}">
                    @else
                        <a href="{{ route('item.detail', ['id' => $item->id]) }}">
                    @endif

                        @if($item->buyer_id)
                            <div class="sold-overlay">SOLD</div>
                        @endif

                        <img src="{{ $item->image }}" alt="{{ $item->item_name }}" class="item-image"/>
                        <p class="item-title">{{ $item->item_name }}</p>
                    </a>
                </div>
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