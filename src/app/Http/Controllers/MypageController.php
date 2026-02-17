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
        
        $perPage = 8;

        // --- 全てのタブで共通の未読チャット数を先に計算する ---
        // 自分が関わる商品（出品者 or 購入者）の中から、
        // 相手からの未読メッセージの数を数える
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
            $soldItems = Item::where('user_id', $user->id)
                             ->whereNotNull('buyer_id')
                             ->get();
            $boughtItems = Item::where('buyer_id', $user->id)->get();
            $mergedItems = $soldItems->merge($boughtItems)->unique('id')->sortByDesc('updated_at');

            $currentPage = Paginator::resolveCurrentPage('page');
            $currentPageItems = $mergedItems->slice(($currentPage - 1) * $perPage, $perPage)->values();

            // ページ内の各商品について、未読メッセージ数を計算して追加する
            foreach ($currentPageItems as $item) {
                $unreadCountForItem = Chat::where('item_id', $item->id)
                                          ->where('user_id', '!=', $user->id)
                                          ->where('is_read', false)
                                          ->count();
                $item->unread_messages_count = $unreadCountForItem;
            }

            // 手動でページネーターを生成
            $items = new LengthAwarePaginator(
                $currentPageItems,
                $mergedItems->count(),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
            );
        }
        
        // --- 最後に全ての変数をビューに渡す ---
        return view('mypage', compact('user', 'activeTab', 'items', 'unreadChatCount'));
    }
}
