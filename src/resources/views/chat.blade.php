@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')
<main class="main-container">
        <!-- サイドバー（スマホでは非表示を想定） -->
        <aside class="sidebar">
            <p>その他の取引</p>
            <!-- 他の取引へのリンクなどをここに配置 -->
        </aside>

        <!-- メインのチャット画面 -->
        <div class="chat-window">
            <div class="chat-header">
                @php
                    if ($item->user_id == Auth::id()) {
                    // 自分が出品者の場合、相手は購入者(buyer)
                        $otherUser = $item->buyer;
                    } else {
                     // 自分が購入者の場合、相手は出品者(user)
                        $otherUser = $item->user;
                    }
                @endphp

             {{-- $otherUserが存在する場合のみ名前を表示 --}}
                <h1>「{{ $otherUser ? $otherUser->username : '相手' }}」さんとの取引画面</h1>
                <button class="btn btn-complete">取引を完了する</button>
            </div>

            <!-- 取引商品情報 -->
            <div class="item-info">
                <img src="{{ $item->image }}" alt="商品画像" class="item-info__image">
                <div class="item-info__details">
                    <p class="item-info__name">{{ $item->item_name }}</p>
                    <p class="item-info__price">¥ {{ number_format($item->price) }}</p>
                </div>
            </div>

            <!-- チャットメッセージエリア -->
            <div class="message-area">
    @foreach($chats as $chat)
        {{-- もし、メッセージの投稿者IDが、ログインしている自分のIDと「同じ」なら --}}
        @if($chat->user_id == Auth::id())
            <!-- ★★★自分のメッセージ (右側)★★★ -->
            <div class="message message--self">
                <div class="message__content">
                    <p class="message__username">{{ $chat->user->username }}</p>
                    <div class="message__bubble">
                        {{-- メッセージ本文を表示 --}}
                        <p>{{ $chat->message }}</p>
                    </div>
                    {{-- 自分のメッセージにだけ編集・削除ボタンを表示 --}}
                    <div class="message__actions">
                        <button class="action-btn">編集</button>
                        @if ($chat->user_id === Auth::id())
                            <form action="{{ route('chat.destroy', ['chat_id' => $chat->id]) }}" method="post" style="display: inline;">
                        @csrf
                        @method('DELETE')
                            <button type="submit" class="action-btn" onclick="return confirm('本当に削除しますか？');">削除                             </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="message__avatar">
                    {{-- ログインユーザー自身のアイコンを表示 --}}
                    <img src="{{ Auth::user()->profile_pic ? asset('storage/' . Auth::user()->profile_pic) : asset('image/default-icon.png') }}" alt="自分のアイコン">
                </div>
            </div>
        @else
            {{-- もし、メッセージの投稿者IDが、自分のものでは「ない」なら --}}
            <!-- ★★★相手のメッセージ (左側)★★★ -->
            <div class="message message--other">
                <div class="message__avatar">
                    {{-- メッセージを投稿したユーザー($chat->user)のアイコンを表示 --}}
                    <img src="{{ $chat->user->profile_pic ? asset('storage/' . $chat->user->profile_pic) : asset('image/default-icon.png') }}" alt="{{ $chat->user->username }}のアイコン">
                </div>
                <div class="message__content">
                    {{-- メッセージを投稿したユーザー($chat->user)の名前を表示 --}}
                    <p class="message__username">{{ $chat->user->username }}</p>
                    <div class="message__bubble">
                        {{-- メッセージ本文を表示 --}}
                        <p>{{ $chat->content }}</p>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>

            <!-- メッセージ入力フォーム -->
            <form action="/item/{{ $item->id }}/chat" method="post" class="message-form">
                @csrf
                <div class="form-group">
                    <input type="text" name="message" class="message-input" placeholder="取引メッセージを記入してください">
                    <label for="image-upload" class="image-upload-label">画像を追加</label>
                    <input type="file" id="image-upload" name="image" class="image-upload-input">
                    <button type="submit" class="send-button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="send-icon">
                            <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
@endsection