<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Condition;
use App\Models\Category;
use App\Models\ItemCategoryCondition;
use App\Models\Like;
use App\Models\UserItemList;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        $userId = auth()->id();

        // 商品を検索する
        $items = Item::where(function($q) use ($query) {
            if ($query) {
            // 商品名に部分一致する場合
            $q->where('item_name', 'LIKE', '%' . $query . '%');
            }
        })
        ->where(function($q) use ($userId) {
            if ($userId) {
                // 自分の商品は表示しない
                $q->where('user_id', '<>', $userId);
            }
        })
            ->paginate(8); // 1ページあたり8件

        return view('item_list', compact('items', 'query'));
    }

    public function search(Request $request)
    {
        // 商品名の部分一致検索
        $query = $request->input('query');
        $items = Item::where('item_name', 'LIKE', '%' . $query . '%')->paginate(8);

        return view('item_list', compact('items', 'query'));
    }

    public function create()
    {
        $conditions = Condition::all();
        $categories = Category::all();
    return view('sell', compact('conditions', 'categories'));
    }

    public function show($id)
    {
    // 商品を取得
        $item = Item::with([
            'comments.user',
            'categoryConditions.category',
            'categoryConditions.condition'
        ])->findOrFail($id);

    // 現在のユーザーIDを取得
        $userId = auth()->id(); // ユーザーがログインしていない場合に備えてnullになることを考慮する

    // いいねのカウントとユーザーのいいね状況を同時に取得
        $likesData = Like::select([
            DB::raw('COUNT(id) as count'),
            DB::raw('MAX(CASE WHEN user_id = ? THEN 1 ELSE 0 END) as userLiked')
        ])
        ->setBindings([$userId]) // バインディングを使用してuser_idを伝達
        ->where('item_id', $id)
        ->first();

        return view('item_detail', [
            'item' => $item,
            'likesCount' => $likesData->count,
            'userLiked' => $likesData->userLiked
        ]);
    }

    public function addToMyList(Request $request, $itemId)
{
    $this->validate($request, [
        'item_id' => 'required|exists:items,id',
    ]);

    UserItemList::create([
        'user_id' => auth()->id(), // 現在ログイン中のユーザーID
        'item_id' => $itemId,
    ]);

    return redirect()->back()->with('success', '商品をマイリストに追加しました。');
}

    public function myList()
{
    // ログイン中のユーザーに対してマイリストを取得
    $items = auth()->user()->likes()->paginate(8); // 1ページあたり8アイテム

    return view('mylist', compact('items'));
}

    public function store(ExhibitionRequest $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'ログインが必要です。');
        }

        // 現在のユーザーのIDを取得
        $userId = auth()->id();

        // 画像をストレージに保存
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        try {
        // 商品データを保存
            $item = Item::create([
                'item_name' => $request->item_name,
                'brand' => $request->brand,
                'description' => $request->description,
                'price' => $request->price,
                'image' => $imagePath,
                'user_id' => $userId,
            ]);

        // カテゴリーと商品の状態をitem_category_conditionテーブルに保存
            if ($request->filled('category_ids') && $request->filled('condition_id')) {
                foreach ($request->category_ids as $categoryId) {
                    ItemCategoryCondition::create([
                        'item_id' => $item->id,
                        'category_id' => $categoryId,
                        'condition_id' => $request->condition_id,
                    ]);
                }
            } else {
                return redirect()->back()->with('error', 'カテゴリーまたは商品状態が選択されていません。');
            }

            return redirect()->route('items.index')->with('success', '商品が出品されました。');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', '商品出品中にエラーが発生しました: ' . $e->getMessage());
        }
    }

//いいね機能
    public function like(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('item.detail', $id)->with('error', 'ログインが必要です。');
        }

        $like = Like::where('user_id', Auth::id())->where('item_id', $id)->first();

        if ($like) {
            // すでにいいねがある場合は削除
            $like->delete();
            return redirect()->route('item.detail', $id)->with('status', 'いいねを解除しました。');
        } else {
            // いいねを新規追加
            Like::create([
                'user_id' => Auth::id(),
                'item_id' => $id,
            ]);
            return redirect()->route('item.detail', $id)->with('status', 'いいねしました。');
        }
    }

    public function getLikes($id)
    {
    // アイテムに対するいいねの数を取得
        $likeCount = Like::where('item_id', $id)->count();
        return response()->json(['likes' => $likeCount]);
    }
}