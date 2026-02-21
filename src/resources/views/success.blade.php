@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/success.css') }}">
@endsection

@section('content')
    <div class="success-container">
        <h1 class="success-title">商品の購入が完了しました！</h1>

        @if ($item)
            <div class="purchased-item">
                <img src="{{ $item->image }}" alt="{{ $item->item_name }}" class="item-image">
                <div class="item-details">
                    <p class="item-name">{{ $item->item_name }}</p>
                    <p class="item-price">¥{{ number_format($item->price) }}</p>
                </div>
            </div>
            <p class="info-message">
                ご利用ありがとうございます。<br>
                出品者からの発送通知をお待ちください。
            </p>
        @else
            <p>ご利用ありがとうございます。</p>
        @endif

        <a class="back-button" href="/">ホームに戻る</a>
    </div>
@endsection