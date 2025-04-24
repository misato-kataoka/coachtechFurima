@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item_list.css') }}">
@endsection

@section('content')
    <div class="header-container">
        <span class="caption">おすすめ</span>
        <span class="caption">
            <a href="{{ route('item.mylist') }}" class="tab-link">マイリスト</a>
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
                    <a href="{{ route('item.detail', ['id' => $item->id]) }}">
                        <img src="{{ $item->image }}" alt="商品画像" class="item-image"/>
                        <div class="item-title">{{ $item->item_name }}</div>
                    </a>
                </div>
                @endforeach
            </div>
        @endif

    <div class="pagination">
    @if ($items->onFirstPage())
        <div class="previous" disabled>前へ</div>
    @else
        <a href="{{ $items->previousPageUrl() }}" class="previous">前へ</a>
    @endif

    @for ($i = 1; $i <= $items->lastPage(); $i++)
        @if ($items->getCollection()->isNotEmpty() && $i === $items->currentPage())
            <div class="active">
                <span>{{ $i }}</span>
            </div>
        @elseif($items->getCollection()->count() > 0 && $items->url($i) == $items->url($items->currentPage()))
            <div class="active">
                <span>{{ $i }}</span>
            </div>
        @else
            <a href="{{ $items->url($i) }}" class="other">{{ $i }}</a>
        @endif
    @endfor

    @if ($items->hasMorePages())
        <a href="{{ $items->nextPageUrl() }}" class="next">次へ</a>
    @else
        <div class="next" disabled>次へ</div>
    @endif
    </div>
</div>
@endsection