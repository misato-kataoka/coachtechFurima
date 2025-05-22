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
                <select name="payment_method" id="payment-method-select">
                    <option value="" disabled selected>支払い方法を選択</option>
                    <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>クレジットカード払い</option>
                    <option value="convenience" {{ old('payment_method') === 'convenience' ? 'selected' : '' }}>コンビニ払い</option>
                </select>
            </div>
            <div class="form__error">
                @error('payment_method')
                {{ $message }}
                @enderror
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

                <div class="form__error">
                    @error('shipping_address')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </form>
    </div>

    <div class="order-summary">
        <h2>注文概要</h2>
        <p><strong>商品代金:</strong> ¥{{ number_format($item->price) }}</p>
        <h2>支払い方法</h2>
        <p>
            <span id="selected-payment-method">未選択</span>
        </p>
        <button id="purchase-button" class="btn btn-danger">購入する</button>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodSelect = document.getElementById('payment-method-select');
        const selectedPaymentMethod = document.getElementById('selected-payment-method');

        paymentMethodSelect.addEventListener('change', function() {
            const value = paymentMethodSelect.value;
            selectedPaymentMethod.textContent = value === 'card' ? 'クレジットカード払い' : 'コンビニ払い';
        });

        // 購入ボタンのイベントリスナー
        document.getElementById('purchase-button').addEventListener('click', function (event) {
            event.preventDefault(); // デフォルトのフォーム送信を防ぐ

            // Stripeを初期化
            const stripe = Stripe('{{ env('STRIPE_KEY') }}'); // 環境変数から公開キーを取得

            // フォームデータを取得
            const paymentMethod = paymentMethodSelect.value;

            if (!paymentMethod) {
                alert('支払い方法を選択してください');
                return;
            }

            // サーバーにリクエストを送信し、セッションを作成
            fetch('/create-checkout-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // CSRFトークンを送信
                },
                body: JSON.stringify({
                    payment_method: paymentMethod,
                    item_price: {{ $item->price }},
                    item_name: '{{ $item->item_name }}'
                }),
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.error) {
                    alert(data.error);
                } else {
                    // Stripe Checkoutにリダイレクト
                    return stripe.redirectToCheckout({ sessionId: data.sessionId });
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('エラーが発生しました。');
            });
        });
    });
</script>
@endsection