@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
<style>
    /* 編集フォーム用の簡単なスタイル */
    .edit-form { display: none; margin-top: 5px; }
    .edit-form textarea { width: 100%; box-sizing: border-box; }
</style>
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
                    <div class="message message--self" id="chat-{{ $chat->id }}">
                        <div class="message__content">
                            <p class="message__username">{{ $chat->user->username }}</p>
                            <div class="message__bubble">
                                <p class="message-text">{{ $chat->message }}</p>
                                {{-- 編集フォーム（初期状態では非表示） --}}
                                <form class="edit-form" action="{{ route('chat.update', ['chat_id' => $chat->id]) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <textarea name="message" rows="3">{{ $chat->message }}</textarea>
                                    <button type="submit">保存</button>
                                    <button type="button" class="cancel-edit-btn">キャンセル</button>
                                </form>
                            </div>
                            {{-- ★★★ 編集・削除ボタンはバブルの外 ★★★ --}}
                            <div class="message__actions">
                                <button type="button" class="action-btn edit-btn">編集</button>
                                <form action="{{ route('chat.destroy', ['chat_id' => $chat->id]) }}" method="post" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn" onclick="return confirm('本当に削除しますか？');">削除</button>
                                </form>
                            </div>
                        </div>
                        <div class="message__avatar {{ Auth::user()->profile_pic ? '' : 'message__avatar--default' }}">
                            <img src="{{ Auth::user()->profile_pic ? asset('storage/' . Auth::user()->profile_pic) : asset('image/default-icon.png') }}" alt="自分のアイコン">
                        </div>
                    </div>
                @else
                    {{-- もし、メッセージの投稿者IDが、自分のものでは「ない」なら --}}
                    <!-- ★★★相手のメッセージ (左側)★★★ -->
                    <div class="message message--other">
                        <div class="message__avatar {{ $chat->user->profile_pic ? '' : 'message__avatar--default' }}">
                            <img src="{{ $chat->user->profile_pic ? asset('storage/' . $chat->user->profile_pic) : asset('image/default-icon.png') }}" alt="{{ $chat->user->username }}のアイコン">
                        </div>
                        <div class="message__content">
                            <p class="message__username">{{ $chat->user->username }}</p>
                            <div class="message__bubble">
                                {{-- ★★★ typo修正: $chat->content -> $chat->message ★★★ --}}
                                <p>{{ $chat->message }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- メッセージ入力フォーム -->
        <form action="{{ route('chat.store', ['item' => $item->id]) }}" method="post" class="message-form">
            @csrf
            <div class="form-group">
                <input type="text" name="message" class="message-input" placeholder="取引メッセージを記入してください">
                <button type="submit" class="send-button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="send-icon"><path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" /></svg>
                </button>
            </div>
        </form>
    </div>
</main>
@endsection

@section('js')
<script>
$(function() {
    // 「編集」ボタンがクリックされた時の処理
    $('.message-area').on('click', '.edit-btn', function() {
        const messageContent = $(this).closest('.message__content');
        messageContent.find('.message-text').hide();
        messageContent.find('.edit-form').show();
        // アクションボタン全体を隠す
        $(this).closest('.message__actions').hide();
    });

    // 「キャンセル」ボタンがクリックされた時の処理
    $('.message-area').on('click', '.cancel-edit-btn', function() {
        const messageContent = $(this).closest('.message__content');
        messageContent.find('.edit-form').hide();
        messageContent.find('.message-text').show();
        // アクションボタンを再表示
        messageContent.find('.message__actions').show();
    });

    // 編集フォームが送信された時の処理 (Ajax)
    $('.message-area').on('submit', '.edit-form', function(e) {
        e.preventDefault();

        const form = $(this);
        const url = form.attr('action');
        const formData = form.serialize();

        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            success: function(response) {
                const messageContent = form.closest('.message__content');
                // 画面の表示を更新
                messageContent.find('.message-text').text(response.updated_message).show();
                messageContent.find('.edit-form').hide();
                messageContent.find('.edit-form textarea').val(response.updated_message);
                // アクションボタンを再表示
                messageContent.find('.message__actions').show();
                console.log(response.message); // 成功メッセージをコンソールに表示
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                let errorMessage = '更新に失敗しました。';
                if (errors && errors.message) {
                    errorMessage += '\n' + errors.message[0];
                }
                alert(errorMessage);
            }
        });
    });
});
</script>
@endsection