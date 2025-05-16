@extends('layouts.app')  

@section('css')  
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">  
@endsection  

@section('content')  
<div class="container">  
    <div class="left-column">  
        <div class="product-info">  
            <div class="image-info-wrapper">  
                <img src="{{ $item->image }}" alt="商品画像" class="item-image"/>  
                <div class="item-details">  
                    <h2>商品名: {{ $item->item_name }}</h2>  
                    <p>価格: ¥{{ number_format($item->price) }}（税込）</p>  
                </div>  
            </div>  
        </div>  

        <form action="{{ route('orders.store') }}" method="POST" id="payment-form">  
            @csrf  
            <div class="payment-method">  
                <h2>支払い方法</h2>  
                <select name="payment_method" required>  
                    <option value="" disabled selected>支払い方法を選択</option>  
                    <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>クレジットカード払い</option>  
                    <option value="convenience" {{ old('payment_method') === 'convenience' ? 'selected' : '' }}>コンビニ払い</option>  
                </select>  
            </div>  

            <div class="shipping-info">  
                <h2>配送先  
                @if (Auth::check())  
                    <a href="{{ route('address.edit', ['item_id' => $item->id]) }}" class="edit-link">変更する</a>  
                @endif  
                </h2>  
                @if(Auth::check())  
                    <p>氏名: {{ Auth::user()->username }}</p>  
                    <p>郵便番号: {{ Auth::user()->post_code }}</p>  
                    <p>住所: {{ Auth::user()->address }}</p>  
                    <p>建物名: {{ Auth::user()->building }}</p>  
                @else  
                    <p>ログインしてください。</p>  
                @endif  
            </div>  
        </form>  
    </div>  

    <!-- order-summaryは左のカラムの外に配置 -->  
    <div class="order-summary">  
        <h2>注文概要</h2>  
        <p><strong>商品代金:</strong> ¥{{ number_format($item->price) }}</p>  
        <h2>支払い方法</h2>  
        <p>  
            @if (old('payment_method'))  
                {{ old('payment_method') === 'card' ? 'クレジットカード払い' : 'コンビニ払い' }}  
            @else  
                未選択  
            @endif  
        </p>  
        <button type="submit" class="btn btn-danger">購入する</button>  
    </div>  
</div>  
@endsection  