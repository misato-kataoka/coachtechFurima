@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/success.css') }}">
@endsection

@section('content')
    <div class="success-message">商品の購入が完了しました！</div>
    <p>ご利用ありがとうございます。</p>
    <a class="back-button" href="/">ホームに戻る</a>
@endsection