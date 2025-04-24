@extends('layouts.app')

@section('content')
<form action="{{ route('orders.store') }}" method="POST" id="payment-form">
    @csrf

    <h2>支払い方法</h2>
    <div>
        <label>
            <input type="radio" name="payment_method" value="card" required>
            クレジットカード払い
        </label>
    </div>
    <div>
        <label>
            <input type="radio" name="payment_method" value="convenience" required>
            コンビニ払い
        </label>
    </div>

    <button type="submit">購入する</button>
</form>
@endsection