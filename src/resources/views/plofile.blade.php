@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile-container">
    <div class="user-info">
        <img src="{{ Auth::user()->profile_pic ? asset('storage/' . Auth::user()->profile_pic) : asset('images/default_user.png') }}" alt="ユーザー画像" class="user-image"/>
        <h1>{{ Auth::user()->username }}</h1>
        <a href="{{ route('address.edit') }}" class="edit-profile-button">プロフィールを編集</a>
    </div>

    <div class="tabs">
        <button class="tab-button active" onclick="showTab('listed')">出品した商品</button>
        <button class="tab-button" onclick="showTab('purchased')">購入した商品</button>
    </div>

    <div class="product-list" id="listed">
        @forelse($listedItems as $item)
            <div class="product-card {{ $item->is_sold ? 'position-relative' : '' }}">
                <a href="{{ route('item.detail', ['item_id' => $item->id]) }}">
                    <img src="{{ $item->image }}" alt="商品画像" class="product-image"/>
                </a>

                @if($item->status !== 'on_sale')
                    <div class="sold-overlay">Sold</div>
                @endif

                <div class="product-info" style="padding: 0 8px;">
                    <h2 class="product-name">{{ $item->item_name }}</h2>
                    <p class="product-price">{{ number_format($item->price) }}円</p>
                </div>
            </div>
        @empty
            <p>出品した商品はありません。</p>
        @endforelse
    </div>

    <div class="product-list" id="purchased" style="display: none;">
        @forelse($purchasedItems as $item)
            <div class="product-card position-relative">
                <a href="{{ route('item.detail', ['item_id' => $item->id]) }}">
                    <img src="{{ $item->image }}" alt="商品画像" class="product-image"/>
                </a>

                @if($item->status !== 'on_sale')
                    <div class="sold-overlay">Sold</div>
                @endif
                <div class="product-info" style="padding: 0 8px;">
                    <h2 class="product-name">{{ $item->item_name }}</h2>
                    <p class="product-price">{{ number_format($item->price) }}円</p>
                </div>
            </div>
        @empty
            <p>購入した商品はありません。</p>
        @endforelse
    </div>

    <!-- ページネーション -->
    <div class="pagination">
        <div id="listed-pagination">
            {{ $listedItems->links() }}
        </div>
        <div id="purchased-pagination" style="display:none;">
            {{ $purchasedItems->links() }}
        </div>
    </div>

<script>
function showTab(tabName) {
    const tabs = document.querySelectorAll('.product-list');
    tabs.forEach(tab => {
        tab.style.display = 'none'; // すべてのタブを非表示に
    });
    document.getElementById(tabName).style.display = 'block'; // 選択されたタブのみ表示

    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.classList.remove('active');
    });
    document.querySelector(`button[onclick="showTab('${tabName}')"]`).classList.add('active');
}
</script>
@endsection