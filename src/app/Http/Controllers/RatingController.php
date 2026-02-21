<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    public function store(Request $request, Item $item)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        // 評価相手のIDを決定
        $evaluatedId = null;
        if ($user->id === $item->buyer_id) {
            $evaluatedId = $item->user_id;
        } else if ($user->id === $item->user_id) {
            $evaluatedId = $item->buyer_id;
        }

        // 不正なリクエストをチェック
        if (!$evaluatedId || $evaluatedId === $user->id) {
            return response()->json([
                'message' => '不正な評価リクエストです。'
            ], 400); // 400 Bad Request
        }

        // すでに評価済みかチェック
        $alreadyRated = Rating::where('evaluator_id', $user->id)
                               ->where('evaluated_id', $evaluatedId)
                               ->where('item_id', $item->id)
                               ->exists();

        if ($alreadyRated) {
            return response()->json([
                'message' => 'すでにこの取引の評価は完了しています。'
            ], 409); // 409 Conflict
        }

        try {
            DB::beginTransaction();

            // 1. 評価をデータベースに保存
            Rating::create([
                'evaluator_id' => $user->id,
                'evaluated_id' => $evaluatedId, // ★★★ ここを $ratedUserId から $evaluatedId に修正 ★★★
                'item_id'      => $item->id,
                'rating'       => $request->rating,
                'comment'      => $request->comment,
            ]);

            // 2. もし「購入者」が評価した場合、商品のステータスを 'completed' に更新
            if ($user->id === $item->buyer_id) {
                $item->status = 'completed';
                $item->save();
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