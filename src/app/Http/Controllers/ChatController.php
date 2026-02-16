<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // --- 認可 (Authorization) ---
        // このチャットページを閲覧できるのは、商品の出品者か購入者のみに限定するべき
        $user = Auth::user();
        if ($user->id !== $item->user_id && $user->id !== $item->buyer_id) {
            // 権限がない場合は、トップページなどにリダイレクトする
            abort(403, 'Unauthorized action.'); 
        }

        // --- データの取得 ---
        // この商品に関連するチャットメッセージをすべて取得する
        // ※リレーションを後で定義する必要があります
        $chats = Chat::where('item_id', $item->id)->orderBy('created_at', 'asc')->get();

        // --- ビューを返す ---
        // 取得したデータと共に、チャットビューを表示する
        return view('chat', [
            'item' => $item,
            'chats' => $chats,
        ]);
    }

    /**
     * チャットメッセージを投稿する
     *
     * @param Request $request
     * @param Item $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Item $item)
    {
        // --- 認可 (Authorization) ---
        $user = Auth::user();
        if ($user->id !== $item->user_id && $user->id !== $item->buyer_id) {
            abort(403, 'Unauthorized action.');
        }

        // --- バリデーション ---
        $request->validate([
            'message' => 'required|string|max:1000', // メッセージは必須、最大1000文字
            'image' => 'nullable|image|max:2048', // 画像は任意、画像ファイルであること、最大2MB
        ]);
    
        $item->chats()->create([
            'user_id' => Auth::id(),
            'message' => $request->message,
            // 画像があれば保存処理を追加
        ]);
        
        // 投稿後、同じチャットページにリダイレクトして戻る
        return back();
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
