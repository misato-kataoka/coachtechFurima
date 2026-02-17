<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreChatRequest;
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

        // --- ビューを返す ---
        // 取得したデータと共に、チャットビューを表示する
        return view('chat', compact('item', 'chats'));
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
        // ポリシーを使って、このメッセージを削除する権限があるかチェック
        $this->authorize('delete', $chat);

        // メッセージを削除
        $chat->delete();

        // 元のチャット画面にリダイレクト
        return back()->with('success', 'メッセージを削除しました。');
    }

    public function update(Request $request, $chat_id)
    {
        // --- バリデーション ---
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // --- 更新対象のメッセージを取得 ---
        $chat = Chat::findOrFail($chat_id);

        // --- 認可 (Policy) ---
        // ログインユーザーがこのメッセージを更新する権限があるかチェック
        $this->authorize('update', $chat);

        // --- データベースを更新 ---
        $chat->message = $request->message;
        $chat->save();

        // --- 成功レスポンスを返す ---
        // Ajax通信なので、ページ全体ではなくJSON形式でデータを返すのが一般的
        return response()->json([
            'success' => true,
            'message' => 'メッセージを更新しました。',
            'updated_message' => $chat->message // 更新後のメッセージを返す
        ]);
    }
}
