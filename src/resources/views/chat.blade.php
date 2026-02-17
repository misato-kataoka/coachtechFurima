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
            {{-- サイドバーのタイトル --}}
            <h3 class="sidebar-title">その他の取引</h3>

            {{-- 商品リストのコンテナ --}}
            <ul class="other-chats-list">
            {{-- コントローラーから渡された$otherChatItemsをループ処理 --}}
                @forelse ($otherChatItems as $otherItem)
                    <li>
                        {{-- 各商品のチャットページへのリンクを生成 --}}
                        <a href="{{ route('chat.show', ['item' => $otherItem->id]) }}" class="other-chat-link">
                            {{-- 商品名を表示 --}}
                            {{ $otherItem->item_name }}
                        </a>
                    </li>
                @empty
                    {{-- 他に取引中の商品が一つもなかった場合に表示されるメッセージ --}}
                    <p class="no-other-chats">他に取引中の商品はありません。</p>
                @endforelse
            </ul>
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
                {{-- アイコンの表示 --}}
                @php
                // 表示するアイコンのパスを決定する
                // $otherUser が存在し、かつ profile_pic がある場合はその画像
                // それ以外の場合は default-icon.png を使う
                    $iconPath = ($otherUser && $otherUser->profile_pic)
                        ? asset('storage/' . $otherUser->profile_pic)
                        : asset('image/default-icon.png');
                @endphp
                <img src="{{ $iconPath }}" alt="ユーザーアイコン" class="chat-header-icon">
             
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
                                {{-- ★★★ 画像がある場合は画像を表示 ★★★ --}}
                                @if($chat->image_path)
                                    <img src="{{ asset('storage/' . $chat->image_path) }}" alt="投稿画像" class="message-image">
                                @endif
                                {{-- ★★★ メッセージがある場合はメッセージを表示 ★★★ --}}
                                @if($chat->message)
                                    <p class="message-text">{{ $chat->message }}</p>
                                @endif
                                {{-- 編集フォーム（初期状態では非表示） --}}
                                <form class="edit-form" action="{{ route('chat.update', ['chat_id' => $chat->id]) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PATCH')

                                    <textarea name="message" rows="3">{{ $chat->message }}</textarea>

                                    <div class="edit-image-controls">
                                        {{-- もし、このメッセージに元々画像があれば、プレビューと削除チェックボックスを表示 --}}
                                        @if($chat->image_path)
                                        <div class="current-image-preview">
                                            <img src="{{ asset('storage/' . $chat->image_path) }}" alt="現在の画像">
                                            <div class="remove-image-wrapper">
                                                {{-- 「画像を削除」チェックボックス --}}
                                                <input type="checkbox" name="remove_image" id="remove-image-{{ $chat->id }}">
                                                <label for="remove-image-{{ $chat->id }}">画像を削除する</label>
                                            </div>
                                        </div>
                                        @endif

                                        {{-- 「画像を変更」するためのファイル選択ボタン --}}
                                        <div class="change-image-wrapper">
                                            <label for="edit-image-{{ $chat->id }}" class="btn-change-image">画像を変更/追加</label>
                                            <input type="file" name="image" id="edit-image-{{ $chat->id }}" class="edit-image-input">
                                        </div>
                                    </div>

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
                                {{-- ★★★ 画像がある場合は画像を表示 ★★★ --}}
                                @if($chat->image_path)
                                    <img src="{{ asset('storage/' . $chat->image_path) }}" alt="投稿画像" class="message-image">
                                @endif
                                {{-- ★★★ メッセージがある場合はメッセージを表示 ★★★ --}}
                                @if($chat->message)
                                    <p>{{ $chat->message }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- メッセージ入力フォーム -->
        <form action="{{ route('chat.store', ['item' => $item->id]) }}" method="post" class="message-form" enctype="multipart/form-data" novalidate data-item-id="{{ $item->id }}">
            @csrf

            @if ($errors->any())
                <div class="validation-errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li class="validation-error-message">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-group">
                {{-- テキストメッセージの入力欄 --}}
                <input type="text" name="message" class="message-input" placeholder="取引メッセージを記入してください" value="{{ old('message') }}">

                {{-- ★★★ 1. これが本体。name="image" を持ち、クラス名を指定する ★★★ --}}
                <input type="file" name="image" id="image-upload" class="image-upload-input">

                {{-- ★★★ 2. これは本体を操作するための「ボタン」。順番を先頭に。 ★★★ --}}
                <label for="image-upload" class="image-upload-label">
                    画像を追加
                </label>

                {{-- 送信ボタン --}}
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

    // 1. 操作対象の要素を特定
    const messageInput = $('.message-input');
    const messageForm = $('.message-form');
    
    // 2. このチャットページ固有の保存キーを生成
    //    itemのIDをbladeから受け取るために、formにdata属性を追加
    const itemId = messageForm.data('item-id');
    const storageKey = `chat-draft-item-${itemId}`;

    // 3. ページ読み込み時の処理
    //    もしLaravelのold()で値がセットされていたら、そちらを優先する
    //    old()が空の場合のみ、sessionStorageから下書きを読み込む
    if (!messageInput.val()) {
        const draft = sessionStorage.getItem(storageKey);
        if (draft) {
            messageInput.val(draft);
        }
    }

    // 4. テキスト入力時の処理
    messageInput.on('input', function() {
        // 入力されるたびに、内容をsessionStorageに保存
        sessionStorage.setItem(storageKey, $(this).val());
    });

    // 5. フォーム送信時の処理
    messageForm.on('submit', function() {
        // メッセージを送信したら、保存しておいた下書きは不要なので削除
        sessionStorage.removeItem(storageKey);
    });

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
        const formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            processData: false,
            contentType: false,
            
            success: function(response) {
                const updatedChat = response.updated_chat; 
                const messageContent = form.closest('.message__content');
                // 画面の表示を更新
                // 1. テキスト部分の更新
            const messageTextElement = messageContent.find('.message-text');
            if (updatedChat.message) {
                messageTextElement.text(updatedChat.message).show();
            } else {
                messageTextElement.text('').hide(); // テキストがなければ非表示
            }

            // 2. 画像部分の更新
            let imageElement = messageContent.find('.message-image');
            if (updatedChat.image_path) {
                const imageUrl = '{{ asset('storage/') }}' + '/' + updatedChat.image_path;
                if (imageElement.length > 0) {
                    // 既存のimgタグのsrcを更新
                    imageElement.attr('src', imageUrl).show();
                } else {
                    // imgタグがなければ、新しく作成して追加
                    messageContent.find('.message__bubble').prepend(`<img src="${imageUrl}" alt="投稿画像" class="message-image">`);
                }
            } else {
                // 画像がなければimgタグを削除
                imageElement.remove();
            }
            
            // 3. フォームとUIの状態をリセット
            form.hide();
            form.find('textarea').val(updatedChat.message);
            // ファイル入力とチェックボックスをリセット
            form.find('.edit-image-input').val('');
            form.find('input[type="checkbox"]').prop('checked', false);
            
            messageContent.find('.message__actions').show(); // アクションボタンを再表示

            console.log(response.message);
        },
        error: function(xhr) {
            // エラー処理は変更なし
            const errors = xhr.responseJSON.errors;
            let errorMessage = xhr.responseJSON.message || '更新に失敗しました。';
            if (errors) {
                // バリデーションエラーメッセージを連結
                Object.keys(errors).forEach(function(key) {
                    errorMessage += '\n' + errors[key][0];
                });
            }
            alert(errorMessage);
        }
    });
});
});
</script>
@endsection