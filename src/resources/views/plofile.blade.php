@extends('layouts.app')  

@section('css')  
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">  
@endsection  

@section('content')  
<div class="profile-container">  
    <div class="user-info">  
        <img src="{{ Auth::user()->profile_image }}" alt="ユーザー画像" class="user-image"/>  
        <h1>{{ Auth::user()->username }}</h1>  
        <a href="{{ route('address.edit') }}" class="edit-profile-button">プロフィールを編集</a>  
    </div>  

    <div class="tabs">  
        <button class="tab-button active" onclick="showTab('listed')">出品した商品</button>  
        <button class="tab-button" onclick="showTab('purchased')">購入した商品</button>  
    </div>  

    <div class="product-list" id="listed">  
        @foreach($listedItems as $item)  
            <div class="product-card">  
                <img src="{{ $item->image }}" alt="商品画像" class="product-image"/>  
                <h2 class="product-name">{{ $item->name }}</h2>  
            </div>  
        @endforeach  
    </div>  

    <div class="product-list" id="purchased" style="display: none;">  
        @foreach($purchasedItems as $item)  
            <div class="product-card">  
                <img src="{{ $item->image }}" alt="商品画像" class="product-image"/>  
                <h2 class="product-name">{{ $item->name }}</h2>  
            </div>  
        @endforeach  
    </div>  

    <!-- ページネーション -->  
    <div class="pagination">  
        {{ $listedItems->links() }} <!-- 出品した商品のページネーション -->  
        {{ $purchasedItems->links() }} <!-- 購入した商品のページネーション -->  
    </div>  
</div>  

<script>  
function showTab(tabName) {  
    const tabs = document.querySelectorAll('.product-list');  
    tabs.forEach(tab => {  
        tab.style.display = 'none'; // すべてのタブを非表示に  
    });  
    document.getElementById(tabName).style.display = 'block'; // 選択されたタブのみ表示  
}  
</script>  
@endsection  