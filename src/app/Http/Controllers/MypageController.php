<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Chat;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MypageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $activeTab = $request->query('tab', 'sell');
        $items = collect();

        $averageRating = $user->ratingsReceived()->avg('rating');

        $roundedAverageRating = $averageRating ? round($averageRating) : 0;

        $activeTab = $request->query('tab', 'sell');
        
        $perPage = 8;

        // --- 全てのタブで共通の未読チャット数を先に計算する ---
        // 自分が関わる商品（出品者 or 購入者）の中から、相手からの未読メッセージの数を数える
        $unreadChatCount = Chat::whereHas('item', function ($query) use ($user) {
                                $query->where(function ($subQuery) use ($user) {
                                    $subQuery->where('user_id', $user->id)
                                             ->orWhere('buyer_id', $user->id);
                                });
                            })
                            ->where('user_id', '!=', $user->id) // 相手からのメッセージ
                            ->where('is_read', false)          // 未読のもの
                            ->count();

        // --- タブごとの処理 ---
        if ($activeTab === 'sell') {
            $items = $user->items()->latest()->paginate($perPage);

        } elseif ($activeTab === 'buy') {
            $items = $user->purchasedItems()->latest()->paginate($perPage);

        } elseif ($activeTab === 'chat') {
            // 1. 取引中の商品（出品・購入）を取得
            $soldItems = Item::where('user_id', $user->id)
                             ->whereNotNull('buyer_id')
                             ->get();
            $boughtItems = Item::where('buyer_id', $user->id)->get();

            // 2. コレクションを結合して重複を排除
            $mergedItems = $soldItems->merge($boughtItems)->unique('id');

            // 3. 各商品に「最新のチャット投稿日時」を追加
            $mergedItems->each(function ($item) {
                $latestChat = Chat::where('item_id', $item->id)
                                  ->latest('created_at')
                                  ->first();
                
                // 最新チャットがあればその日時を、なければ商品の更新日時を代替として使用
                $item->last_activity_at = $latestChat ? $latestChat->created_at : $item->updated_at;
            });

            // 4. 「最新の活動日時」でコレクションを降順ソート
            $sortedItems = $mergedItems->sortByDesc('last_activity_at');
            
            // 5. 手動でページネーションを生成
            $currentPage = Paginator::resolveCurrentPage('page');
            $currentPageItems = $sortedItems->slice(($currentPage - 1) * $perPage, $perPage)->values();

            // 6. ページ内の各商品について、未読メッセージ数を計算して追加する
            foreach ($currentPageItems as $item) {
                $item->unread_messages_count = Chat::where('item_id', $item->id)
                                                   ->where('user_id', '!=', $user->id)
                                                   ->where('is_read', false)
                                                   ->count();
            }

            // 7. 手動でページネーターを生成
            $items = new LengthAwarePaginator(
                $currentPageItems,
                $sortedItems->count(),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
            );
        }

        return view('mypage', [
        'items' => $items,
        'activeTab' => $activeTab,
        'unreadChatCount' => $unreadChatCount,
        'roundedAverageRating' => $roundedAverageRating,
        ]);
    }
}
