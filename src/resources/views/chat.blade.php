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
                        <a href="{{ route('chat.show', ['item' => $otherItem->id]) }}" class="other-chat-link">
                            {{-- 商品名を表示 --}}
                            <span class="other-chat-link__name">{{ $otherItem->item_name }}</span>
                    
                            {{-- 未読メッセージ数が1以上あれば、バッジを表示する --}}
                            @if ($otherItem->unread_count > 0)
                                <span class="other-chat-link__badge">{{ $otherItem->unread_count }}</span>
                            @endif
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
                
                @if ($isAlreadyRated)
                    {{-- 評価済みの場合のボタン --}}
                    <button class="btn btn-complete" disabled>評価済みです</button>
                @else
                    {{-- 未評価の場合のボタン (これまで通り) --}}
                    <button class="btn btn-complete">取引を完了する</button>
                @endif
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
                                                <input type="checkbox" name="remove_image" id="remove-image-{{ $chat->id }}" value="true">
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
                            {{--  編集・削除ボタン --}}
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
                    <!-- 相手のメッセージ (左側) -->
                    <div class="message message--other">
                        <div class="message__avatar {{ $chat->user->profile_pic ? '' : 'message__avatar--default' }}">
                            <img src="{{ $chat->user->profile_pic ? asset('storage/' . $chat->user->profile_pic) : asset('image/default-icon.png') }}" alt="{{ $chat->user->username }}のアイコン">
                        </div>
                        <div class="message__content">
                            <p class="message__username">{{ $chat->user->username }}</p>
                            <div class="message__bubble">
                                {{--  画像がある場合は画像を表示  --}}
                                @if($chat->image_path)
                                    <img src="{{ asset('storage/' . $chat->image_path) }}" alt="投稿画像" class="message-image">
                                @endif
                                {{--  メッセージがある場合はメッセージを表示  --}}
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

                <input type="file" name="image" id="image-upload" class="image-upload-input">

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

<div id="rating-modal" class="rating-modal" style="display: none;">
    <div class="rating-modal__content">
        
        <h2 class="rating-modal__title">取引が完了しました。</h2>
        
        {{-- 評価フォーム --}}
        <form id="rating-form" class="rating-form-body">
            @csrf
            
            <p class="rating-modal__subtitle">今回の取引相手はどうでしたか？</p>

            {{-- ★★★ 5段階評価の星 ★★★ --}}
            <div class="rating-stars">
                <span class="star" data-value="1">★</span>
                <span class="star" data-value="2">★</span>
                <span class="star" data-value="3">★</span>
                <span class="star" data-value="4">★</span>
                <span class="star" data-value="5">★</span>
                <input type="hidden" name="rating" id="rating-value" value="">
            </div>
            
            {{-- バリデーションエラー表示用 --}}
            <div id="rating-errors" class="validation-errors" style="display: none;"></div>

            <div class="rating-modal__footer">
                <button type="submit" class="btn btn-submit-rating">送信する</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
$(function() {

    // ===================================
    //  下書き保存 & メッセージ編集のJS
    // ===================================

    // 1. 操作対象の要素を特定
    const messageInput = $('.message-input');
    const messageForm = $('.message-form');
    
    // 2. このチャットページ固有の保存キーを生成
    const itemId = messageForm.data('item-id');
    const storageKey = `chat-draft-item-${itemId}`;

    // 3. ページ読み込み時の処理
    if (!messageInput.val()) {
        const draft = sessionStorage.getItem(storageKey);
        if (draft) {
            messageInput.val(draft);
        }
    }

    // 4. テキスト入力時の処理
    messageInput.on('input', function() {
        sessionStorage.setItem(storageKey, $(this).val());
    });

    // 5. フォーム送信時の処理
    messageForm.on('submit', function() {
        sessionStorage.removeItem(storageKey);
    });

    // 「編集」ボタンがクリックされた時の処理
    $('.message-area').on('click', '.edit-btn', function() {
        const messageContent = $(this).closest('.message__content');
        messageContent.find('.message-text').hide();
        messageContent.find('.edit-form').show();
        $(this).closest('.message__actions').hide();
    });

    // 「キャンセル」ボタンがクリックされた時の処理
    $('.message-area').on('click', '.cancel-edit-btn', function() {
        const messageContent = $(this).closest('.message__content');
        messageContent.find('.edit-form').hide();
        messageContent.find('.message-text').show();
        messageContent.find('.message__actions').show();
    });

    // 編集フォームが送信された時の処理 (Ajax)
    $('.message-area').on('submit', '.edit-form', function(e) {
    e.preventDefault(); // フォームのデフォルト送信をキャンセル

    const form = $(this);
    const url = form.attr('action');

    // 1. new FormData() でフォーム要素から基本データを作成
    //    これには message と image (ファイル) が含まれます
    const formData = new FormData(this);

    // 2.【最重要】_methodを手動で追加
    const method = form.find('input[name="_method"]').val();
    formData.append('_method', method);

    // 3.【重要】remove_imageの値を手動で設定
    //    チェックボックスの状態に応じて 'true' または 'false' の【文字列】を設定
    const isRemoveChecked = form.find('input[name="remove_image"]').is(':checked');
    // formData.set() を使うことで、キーが存在しない場合でも確実に追加/上書き
    formData.set('remove_image', isRemoveChecked ? 'true' : 'false');


    // 4. Ajaxで送信
    $.ajax({
        type: 'POST', // typeは'POST'のまま。Laravelは_methodを見て判断します。
        url: url,
        data: formData,
        processData: false, // FormDataを送信するため必須
        contentType: false, // FormDataを送信するため必須
        success: function(response) {
            // Controllerからのレスポンスが成功(success: true)の場合
            if (response.success) {
                const updatedChat = response.updated_chat;
                const messageContent = form.closest('.message__content');
                const messageBubble = messageContent.find('.message__bubble');

                // --- テキストの更新 ---
                const messageTextElement = messageBubble.find('.message-text');
                if (updatedChat.message) {
                    messageTextElement.text(updatedChat.message).show();
                } else {
                    messageTextElement.hide().text(''); // テキストがなければ隠す
                }

                // --- 画像の更新 ---
                let imageElement = messageBubble.find('.message-image');
                if (updatedChat.image_path) {
                    // 新しい画像パスがあれば、画像のURLを生成
                    const imageUrl = '{{ asset('storage/') }}' + '/' + updatedChat.image_path;
                    if (imageElement.length > 0) {
                        // 既存のimgタグがあればsrcを更新
                        imageElement.attr('src', imageUrl).show();
                    } else {
                        // 既存のimgタグがなければ、新しくprepend（先頭に追加）
                        messageBubble.prepend(`<img src="${imageUrl}" alt="投稿画像" class="message-image">`);
                    }
                } else {
                    // 新しい画像パスがなければ、imgタグを削除
                    imageElement.remove();
                }

                // --- フォームの状態をリセット ---
                form.hide(); // 編集フォームを隠す
                form.find('textarea').val(updatedChat.message); // 次回の編集のためにtextareaを更新
                form.find('.edit-image-input').val(''); // ファイル選択をクリア
                form.find('input[type="checkbox"]').prop('checked', false); // チェックボックスをクリア
                messageContent.find('.message__actions').show(); // 編集・削除ボタンを再表示

                console.log(response.message); // "メッセージを更新しました。"
            } else {
                 // Controllerが success: false で返した場合 (例: メッセージと画像が両方空)
                alert(response.message);
            }
        },
        error: function(xhr) {
            // バリデーションエラーやその他のサーバーエラー
            const response = xhr.responseJSON;
            let errorMessage = response.message || '更新に失敗しました。';
            if (response.errors) {
                // バリデーションエラーメッセージを連結
                Object.keys(response.errors).forEach(function(key) {
                    errorMessage += '\n' + response.errors[key][0];
                });
            }
            alert(errorMessage);
        }
    });
});

    // ===================================
    //  評価モーダルのJS
    // ===================================

    // === 変数定義 ===
    const ratingModal = $('#rating-modal');
    const stars = $('.star');
    const ratingValueInput = $('#rating-value');
    const ratingForm = $('#rating-form');
    const submitButton = $('.btn-submit-rating');
    const ratingErrors = $('#rating-errors');

    // === 関数定義 ===
    function openRatingModal() {
        ratingModal.css('display', 'flex');
    }

    function closeRatingModal() {
        ratingModal.hide();
        stars.removeClass('selected');
        ratingValueInput.val('');
        submitButton.prop('disabled', true);
        ratingErrors.hide().empty();
    }

    // === イベントリスナー ===
    $('.btn-complete').on('click', function(e) {
        e.preventDefault();

        // もしボタンが disabled なら、何もしない
        if ($(this).is(':disabled')) {
            alert('すでに評価済みです。');
            return; 
        }       
        openRatingModal();
    });

    stars.on('mouseover', function() {
        const value = $(this).data('value');
        stars.removeClass('hover');
        stars.slice(0, value).addClass('hover');
    }).on('mouseout', function() {
        stars.removeClass('hover');
    });

    stars.on('click', function() {
        const value = $(this).data('value');
        ratingValueInput.val(value);
        stars.removeClass('selected');
        stars.slice(0, value).addClass('selected');
        submitButton.prop('disabled', false);
        ratingErrors.hide().empty();
    });
    
    ratingForm.on('submit', function(e) {
        e.preventDefault();
        submitButton.prop('disabled', true).text('送信中...');
        
        const formData = $(this).serialize();
        const postUrl = `/item/{{ $item->id }}/rating`;

        $.ajax({
            url: postUrl,
            type: 'POST',
            data: formData,
            dataType: 'json',
        })
        .done(function(response) {
            alert(response.message);
            window.location.href = response.redirect_url;
        })
        .fail(function(jqXHR) {
            ratingErrors.show().empty();
            if (jqXHR.status === 422) {
                const errors = jqXHR.responseJSON.errors;
                let errorHtml = '<ul>';
                $.each(errors, function(key, value) {
                    errorHtml += `<li class="validation-error-message">${value[0]}</li>`;
                });
                errorHtml += '</ul>';
                ratingErrors.html(errorHtml);
            } else {
                const message = jqXHR.responseJSON?.message || 'エラーが発生しました。時間をおいて再度お試しください。';
                ratingErrors.html(`<p class="validation-error-message">${message}</p>`);
            }
        })
        .always(function() {
            if (ratingForm.find('.btn-submit-rating').text() === '送信中...') {
                submitButton.prop('disabled', false).text('送信する');
            }
        });
    });

    // 初期状態では送信ボタンを無効化
    submitButton.prop('disabled', true);

    // === ページ読み込み時の処理 ===
    @if($shouldShowRatingModal)
        openRatingModal();
    @endif

});
</script>
@endsection