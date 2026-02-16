<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MypageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        // クエリパラメータから 'tab' を取得。なければ 'sell' をデフォルトにする
        $activeTab = $request->query('tab', 'sell');

        $items = collect(); // 表示するアイテムを初期化

        $perPage = 8;

        if ($activeTab === 'sell') {
            // 出品した商品は paginate() を直接使える
            $items = $user->items()->latest()->paginate($perPage);
        } elseif ($activeTab === 'buy') {
            // 購入した商品も paginate() を直接使える
            $items = $user->purchasedItems()->latest()->paginate($perPage);
        } elseif ($activeTab === 'chat') {
            // 取引中の商品は手動でページネーションを作成する

            // 自分が売った取引中の商品
            $soldItems = Item::where('user_id', $user->id)
                             ->whereNotNull('buyer_id')
                             ->get();
            // 自分が買った商品
            $boughtItems = Item::where('buyer_id', $user->id)->get();

            // 結合し、重複を排除し、新しい順に並び替える
            $mergedItems = $soldItems->merge($boughtItems)->unique('id')->sortByDesc('updated_at');

            // 現在のページ番号を取得
            $currentPage = Paginator::resolveCurrentPage('page');

            // コレクションを指定した数で分割し、現在のページ部分のみを取得
            $currentPageItems = $mergedItems->slice(($currentPage - 1) * $perPage, $perPage)->values();

            // Paginatorインスタンスを生成
            $items = new LengthAwarePaginator(
                $currentPageItems,    // 現在のページに表示するデータ
                $mergedItems->count(), // 全体のアイテム数
                $perPage,             // 1ページあたりの表示件数
                $currentPage,         // 現在のページ番号
                ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()] // URLを正しく生成するためのオプション
            );
        }

        return view('mypage', [
            'items' => $items,
            'activeTab' => $activeTab
        ]);
    }
}
