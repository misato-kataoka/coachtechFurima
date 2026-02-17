<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreChatRequest;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Chat;

class ChatController extends Controller
{
    /**
     * 特定の商品のチャットページを表示する
     *
     * @param Item $item ルートモデルバインディングにより、URLのIDからItemインスタンスが自動的に渡される
     * @return \Illuminate\View\View
     */
    public function show(Item $item)
    {
        $this->authorize('view', $item); 
    
        // --- 認可 (Authorization) ---
        // このチャットページを閲覧できるのは、商品の出品者か購入者のみに限定するべき
        $user = Auth::user();
        if ($user->id !== $item->user_id && $user->id !== $item->buyer_id) {
            // 権限がない場合は、トップページなどにリダイレクトする
            abort(403, 'Unauthorized action.'); 
        }

        // このチャットルームに含まれる、相手からの未読メッセージをすべて既読にする
        Chat::where('item_id', $item->id)
            ->where('user_id', '!=', $user->id) // 相手が送信したメッセージ
            ->where('is_read', false)          // 未読のもの
            ->update(['is_read' => true]);     // 既読に更新

        // --- データの取得 ---
        // この商品に関連するチャットメッセージをすべて取得する
        // ※リレーションを後で定義する必要があります
        $chats = Chat::where('item_id', $item->id)->orderBy('created_at', 'asc')->get();

        $user = Auth::user();

        // 1. 自分が「出品者」として取引中の商品リストを取得
        $sellingItems = Item::where('user_id', $user->id)
            ->whereNotNull('buyer_id') // 購入者がいるもの
            ->get();

        // 2. 自分が「購入者」として取引中の商品リストを取得
        $buyingItems = Item::where('buyer_id', $user->id)
            ->get();

        // 3. ２つのリストを結合し、重複を削除し、現在表示中の商品を除外する
        $otherChatItems = $sellingItems->merge($buyingItems) // 結合
            ->unique('id')         // 重複を削除
            ->where('id', '!=', $item->id); // 現在の商品を除外


        // --- ビューを返す ---
        // 取得したデータと共に、チャットビューを表示する
        return view('chat', [
            'item' => $item,
            'chats' => $chats,
            'otherChatItems' => $otherChatItems,
        ]);
    }

    /**
     * チャットメッセージを投稿する
     *
     * @param Request StoreChatRequest $request
     * @param Item $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreChatRequest $request, Item $item)
    {
        $validatedData = $request->validated();

    // メッセージも画像も空の場合は、何もせず元のページに戻る
    if (!$request->filled('message') && !$request->hasFile('image')) {
        return back();
    }

    // --- データベースへの保存処理 ---

    $chat = new Chat();
        $chat->user_id = Auth::id();
        $chat->item_id = $item->id;
        $chat->message = $validatedData['message'];

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

        // ★★★ メッセージに画像があればストレージから削除する処理を追加 ★★★
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
            // メッセージ削除はdestroyメソッドで行うべきなので、updateではエラーとする
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