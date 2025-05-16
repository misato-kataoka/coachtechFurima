@extends('layouts.app')  

@section('css')  
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">  
@endsection  

@section('content')  
<div class="header-container">  
    <span class="caption">出品した商品</span>  
    <span class="caption">  
        <a href="{{ route('item.mylist') }}" class="tab-link">購入した商品</a>  
    </span>  
</div>  
<div class="border-line"></div>  

<div class="container">  
    @if ($purchasedItems->isEmpty()) <!-- ここを修正 -->  
        <div class="no-items-message">商品がありません。</div>  
    @else  
        <div class="item-grid">  
            @foreach ($purchasedItems as $item) <!-- ここを修正 -->  
                <div class="item-card">  
                    <a href="{{ route('item.show', ['id' => $item->id]) }}">  
                        <img src="{{ $item->image }}" alt="商品画像" class="item-image"/>  
                        <div class="item-title">{{ $item->item_name }}</div>  
                    </a>  
                </div>  
            @endforeach  
        </div>  
    @endif  
</div>

    <div class="pagination">  
        @if ($purchasedItems->onFirstPage())  
            <span class="disabled">前へ</span>  
        @else  
            <a href="{{ $items->previousPageUrl() }}" class="previous">前へ</a>  
        @endif  

        @for ($i = 1; $i <= $purchasedItems->lastPage(); $i++)  
            @if ($i === $purchasedItems->currentPage())  
                <span class="active">{{ $i }}</span>  
            @else  
                <a href="{{ $items->url($i) }}" class="other">{{ $i }}</a>  
            @endif  
        @endfor  

        @if ($purchasedItems->hasMorePages())  
            <a href="{{ $items->nextPageUrl() }}" class="next">次へ</a>  
        @else  
            <span class="disabled">次へ</span>  
        @endif  
    </div>  
</div>  
@endsection  