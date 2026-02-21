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

        $evaluator = Auth::user(); // 評価する人 = ログインしているユーザー
        $evaluatedId = null;       // 評価される人のID

        $isSellerRating = $request->boolean('is_seller_rating');

        if ($isSellerRating) {
            // 【出品者からの評価の場合】
            // 権限チェック：ログインユーザーは出品者か？
            if ($evaluator->id !== $item->user_id) { // user_id は出品者ID
                return response()->json(['message' => '不正なリクエストです。あなたはこの商品の出品者ではありません。'], 403);
            }
            // 被評価者は購入者
            $evaluatedId = $item->buyer_id;
        } else {
            // 【購入者からの評価の場合】
            // 権限チェック：ログインユーザーは購入者か？
            if ($evaluator->id !== $item->buyer_id) {
                return response()->json(['message' => '不正なリクエストです。あなたはこの商品の購入者ではありません。'], 403);
            }
            // 被評価者は出品者
            $evaluatedId = $item->user_id;
        }

        // 被評価者が特定できない場合はエラー
        if (!$evaluatedId) {
            return response()->json(['message' => '評価対象のユーザーが見つかりません。'], 404);
        }

        // --- 3. 重複評価のチェック  ---
        $alreadyRated = Rating::where('item_id', $item->id)
                               ->where('evaluator_id', $evaluator->id)
                               ->exists();

        if ($alreadyRated) {
            return response()->json(['message' => 'すでにこの取引の評価は完了しています。'], 409);
        }

        // --- 4. データベースへの保存処理  ---
        try {
            DB::beginTransaction();

            Rating::create([
                'evaluator_id' => $evaluator->id,  // 評価者
                'evaluated_id' => $evaluatedId,    // 被評価者 
                'item_id'      => $item->id,
                'rating'       => $request->rating,
                'comment'      => $request->comment,
            ]);

            // 通知とステータス更新の分岐
            if ($isSellerRating) {
                // 出品者が評価した場合 → 取引完了
                $item->status = 'completed'; // 商品ステータスを「取引完了」に
                $item->save();

                // 購入者に「取引が完了しました」と通知
                $buyer = User::find($evaluatedId);
                if ($buyer) {
                    $buyer->notify(new TransactionCompletedNotification($item->id,$evaluatedId)); // 通知クラスに合わせて引数を調整
                }
            } else {
                // 購入者が評価した場合
                // (必要であれば)出品者に「購入者から評価されました」と通知
                // $seller = User::find($evaluatedId);
                // if ($seller) {
                //     $seller->notify(new BuyerRatedNotification($item));
                // }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('評価処理中にエラー: ' . $e->getMessage());
            return response()->json(['message' => '評価の保存中にエラーが発生しました。'], 500);
        }
        
        // --- 5. 成功レスポンス  ---
        return response()->json([
            'message'      => '評価を送信しました。ありがとうございました。',
            'redirect_url' => route('chat.show', ['item' => $item->id])
        ], 200);
    }
}