<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\TransactionCompletedNotification;

class RatingController extends Controller
{
    public function store(Request $request, Item $item)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $buyer = Auth::user();

        // 評価相手（出品者）のIDを決定
        $sellerId = null;
        if ($buyer->id === $item->buyer_id) {
            $sellerId = $item->user_id;
        }

        // 購入者が評価する場合以外は、この先の処理に進めないようにする
        if (!$sellerId) {
            return response()->json([
                'message' => '不正な評価リクエストです。'
            ], 400);
        }

        // すでに評価済みかチェック
        $alreadyRated = Rating::where('evaluator_id', $buyer->id)
                               ->where('evaluated_id', $sellerId)
                               ->where('item_id', $item->id)
                               ->exists();

        if ($alreadyRated) {
            return response()->json([
                'message' => 'すでにこの取引の評価は完了しています。'
            ], 409);
        }

        try {
            DB::beginTransaction();

            // 1. 評価をデータベースに保存
            Rating::create([
                'evaluator_id' => $buyer->id,
                'evaluated_id' => $sellerId,
                'item_id'      => $item->id,
                'rating'       => $request->rating,
                'comment'      => $request->comment,
            ]);

            // 2. 商品のステータスを 'completed' に更新
            $item->status = 'completed';
            $item->save();

            // 3. 出品者を取得して通知を送信
            $seller = User::find($sellerId);
            if ($seller) {
                // 出品者に通知（引数には商品と購入者の情報を持たせる）
                $seller->notify(new TransactionCompletedNotification($item->id, $buyer->id));
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json([
                'message' => '評価の保存中にエラーが発生しました。もう一度お試しください。'
            ], 500);
        }

        // 成功時のJSONレスポンス
        return response()->json([
            'message'      => '評価を送信しました。ありがとうございました。',
            'redirect_url' => route('chat.show', ['item' => $item->id])
        ]);
    }
}