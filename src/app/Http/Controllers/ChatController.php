<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreChatRequest;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Chat;
use App\Models\Rating;

class ChatController extends Controller
{
    /**
     * 特定の商品のチャットページを表示する
     *
     * @param Item $item 
     * @return \Illuminate\View\View
     */
    public function show(Item $item)
    {
        $this->authorize('view', $item); 
        $user = Auth::user();

        // --- 未読メッセージを既読に更新 ---
        Chat::where('item_id', $item->id)
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // --- チャットメッセージ一覧を取得 ---
        $chats = Chat::with('user')->where('item_id', $item->id)->orderBy('created_at', 'asc')->get();

        // --- 他のチャットルーム一覧用のデータを取得 ---
        // ログインユーザーが出品者である取引と、購入者である取引を両方取得
        $sellingItems = Item::where('user_id', $user->id)->whereNotNull('buyer_id')->get();
        $buyingItems = Item::where('buyer_id', $user->id)->get();
        // ２つのコレクションを結合し、重複を除外し、現在のチャットルームは除外する
        $mergedItems = $sellingItems->merge($buyingItems)
                                      ->unique('id')
                                      ->where('id', '!=', $item->id);

        // 各商品に「最新のチャット投稿日時」と「未読メッセージ数」を追加
        $mergedItems->each(function ($chatItem) use ($user) {
            // 最新のチャットを1件だけ取得
            $latestChat = Chat::where('item_id', $chatItem->id)->latest('created_at')->first();
            
            // 並び替え用の日時を設定 (最新チャットがなければ商品の更新日時を使用)
            $chatItem->last_activity_at = $latestChat ? $latestChat->created_at : $chatItem->updated_at;

            // サイドバー表示用の未読メッセージ数を計算
            $chatItem->unread_count = Chat::where('item_id', $chatItem->id)
                                          ->where('user_id', '!=', $user->id)
                                          ->where('is_read', false)
                                          ->count();
        });

        // 最新の活動日時でコレクションを降順
        $otherChatItems = $mergedItems->sortByDesc('last_activity_at');                              

        // --- 評価関連のロジック ---

        // ログインユーザーが、この取引において出品者か購入者かを判定
        $isSeller = ($user->id === $item->user_id);
        $isBuyer = ($user->id === $item->buyer_id);

        // 自分が（相手を）すでに評価済みかどうかを判定
        $isAlreadyRated = false;
        if ($isSeller) {
            // 自分(出品者)が購入者を評価した記録があるか
            $isAlreadyRated = Rating::where('item_id', $item->id)
                                    ->where('evaluator_id', $user->id)
                                    ->where('evaluated_id', $item->buyer_id)
                                    ->exists();
        } elseif ($isBuyer) {
            // 自分(購入者)が出品者を評価した記録があるか
            $isAlreadyRated = Rating::where('item_id', $item->id)
                                    ->where('evaluator_id', $user->id)
                                    ->where('evaluated_id', $item->user_id)
                                    ->exists();
        }

        // 評価モーダルを自動表示すべきか判定
        $shouldShowRatingModal = false;
        // もし自分が「出品者」なら、モーダルを自動表示すべきか判定する
        if ($isSeller) {
            // 条件A: 購入者からの評価が存在するか？
            $hasBuyerRated = Rating::where('item_id', $item->id)
                                   ->where('evaluator_id', $item->buyer_id) 
                                   ->where('evaluated_id', $user->id)
                                   ->exists();

            // 条件B: 自分はまだ評価していない
            // 両方の条件を満たす場合にモーダルを自動表示する
            if ($hasBuyerRated && !$isAlreadyRated) {
                $shouldShowRatingModal = true;
            }
        }

        return view('chat', [
            'item' => $item,
            'chats' => $chats,
            'otherChatItems' => $otherChatItems,
            'isAlreadyRated' => $isAlreadyRated,
            'shouldShowRatingModal' => $shouldShowRatingModal,
        ]);
    }

    /**
     * チャットメッセージを投稿する
     *
     * @param StoreChatRequest $request
     * @param Item $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreChatRequest $request, Item $item)
    {
        $this->authorize('create', [Chat::class, $item]);

        $validatedData = $request->validated();
    // --- データベースへの保存処理 ---
        $chat = new Chat();
        $chat->user_id = Auth::id();
        $chat->item_id = $item->id;
        $chat->message = $validatedData['message'] ?? null;

        // 画像があれば保存し、パスを設定
        if ($request->hasFile('image')) {
            // 'public'ディスクの'chat_images'フォルダに保存し、そのパスを$pathに格納
            $path = $request->file('image')->store('chat_images', 'public');
            $chat->image_path = $path;
        }

        // すべての設定が終わった$chatをデータベースに保存
        $chat->save();

        // チャット画面にリダイレクトする
        return redirect()->route('chat.show', ['item' => $item->id]);
    }

    /**
 * チャットメッセージを削除する
 */
    public function destroy($chat_id)
    {
        $chat = Chat::findOrFail($chat_id);
        $this->authorize('delete', $chat);

        if ($chat->image_path) {
            Storage::disk('public')->delete($chat->image_path);
        }
        
        $chat->delete();

        return back()->with('success', 'メッセージを削除しました。');
    }

    public function update(Request $request, $chat_id)
    {
        // --- バリデーション ---
        $validatedData = $request->validate([
            'message' => 'required_without_all:image,remove_image|nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);

        $chat = Chat::findOrFail($chat_id);
        $this->authorize('update', $chat);

        // --- テキストメッセージの更新 ---
        $chat->message = $validatedData['message'] ?? null;

        // --- 画像の削除処理 ---
        if ($request->boolean('remove_image') && !$request->hasFile('image')) {
            if ($chat->image_path) {
                Storage::disk('public')->delete($chat->image_path);
                $chat->image_path = null;
            }
        }

        // --- 新しい画像のアップロード処理 ---
        if ($request->hasFile('image')) {
            if ($chat->image_path) {
                Storage::disk('public')->delete($chat->image_path);
            }
            $path = $request->file('image')->store('chat_images', 'public');
            $chat->image_path = $path;
        }
        
        // --- メッセージと画像が両方空になるのを防ぐ最終チェック ---
        if (empty($chat->message) && empty($chat->image_path)) {
            return response()->json([
                'success' => false,
                'message' => 'メッセージと画像の両方を空にすることはできません。削除する場合は削除ボタンを使用してください。',
            ], 422);
        }

        $chat->save();

        // --- 成功レスポンスを返す ---
        return response()->json([
            'success' => true,
            'message' => 'メッセージを更新しました。',
            'updated_chat' => [
                'message' => $chat->message,
                'image_path' => $chat->image_path,
            ]
        ]);
    }

}