@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
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

<div class="tab-container">
    <a href="{{ url('/mypage?tab=sell') }}" class="tab-link {{ $activeTab === 'sell' ? 'active' : '' }}">出品した商品</a>
    <a href="{{ url('/mypage?tab=buy') }}" class="tab-link {{ $activeTab === 'buy' ? 'active' : '' }}">購入した商品</a>
</div>
<div class="border-line"></div>

<div class="container">
    @if ($itemsToShow->isEmpty())
        <div class="no-items-message">商品がありません。</div>
    @else
        <div class="item-grid">
            @foreach ($itemsToShow as $item)
                <div class="item-card">
                    <a href="{{ route('item.detail', ['id' => $item->id]) }}">
                        <img src="{{ $item->image }}" alt="商品画像" class="item-image"/>
                        <div class="item-title">{{ $item->item_name }}</div>
                    </a>

                    @if( ($activeTab === 'sell' && $item->is_sold) || $activeTab === 'buy' )
                        <div class="sold-overlay">SOLD</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

<div class="pagination">  
    @if ($itemsToShow->onFirstPage())  
        <span class="disabled">前へ</span>  
    @else  
        <a href="{{ $itemsToShow->previousPageUrl() }}" class="previous">前へ</a>  
    @endif  

    @for ($i = 1; $i <= $itemsToShow->lastPage(); $i++)  
        @if ($i === $itemsToShow->currentPage())  
            <span class="active">{{ $i }}</span>  
        @else  
            <a href="{{ $itemsToShow->url($i) }}" class="other">{{ $i }}</a>  
        @endif  
    @endfor  

    @if ($itemsToShow->hasMorePages())  
        <a href="{{ $itemsToShow->nextPageUrl() }}" class="next">次へ</a>  
    @else  
        <span class="disabled">次へ</span>  
    @endif  
</div>  
@endsection