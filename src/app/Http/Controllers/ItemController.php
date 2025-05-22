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
use App\Models\Like;
use App\Models\UserItemList;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');

        // 商品を検索する
        if ($query) {
            // 商品名に部分一致するものを検索し、ページネートする
            $items = Item::where('item_name', 'LIKE', '%' . $query . '%')->paginate(8); // 1ページあたり8件
        } else {
            // クエリがない場合はすべての商品を取得し、ページネートする
            $items = Item::paginate(8); // 1ページあたり8件
        }

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
    /*public function myList()  
    {  
        // セッションやデータベースから商品を取得する  
        $items = Item::where('user_id', auth()->id())->paginate(8);

        return view('mylist', compact('items'));  
    } */

    public function show($id)
    {
    $item = Item::with('comments.user', 'categoryConditions.category', 'categoryConditions.condition')->findOrFail($id); 
    $likesCount = Like::where('item_id', $id)->count();

    // ユーザーが既にいいねしているかを確認  
        $userLiked = Like::where('item_id', $id)->where('user_id', auth()->id())->exists();
    return view('item_detail', compact('item', 'likesCount', 'userLiked')); // item_detail ビューに渡す
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
    $items = UserItemList::where('user_id', auth()->id())  
                ->with('item') // 商品情報も同時に取得  
                ->paginate(8); // 1ページあたり10アイテム表示  

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

        // カテゴリーの関連付け
            if ($request->filled('category_ids')) {
                $item->categories()->attach($request->category_ids);
            } else {
                return redirect()->back()->with('error', 'カテゴリーが選択されていません。');
            }

        // カテゴリーと状態をitem_category_conditionに保存
            if ($request->filled('condition_id')) {
                foreach ($request->category_ids as $categoryId) {
                    ItemCategoryCondition::create([
                        'item_id' => $item->id,
                        'category_id' => $categoryId,
                        'condition_id' => $request->condition_id,
                    ]);
                }
            } else {
                return redirect()->back()->with('error', '商品状態が選択されていません。');
            }

            return redirect()->route('items.index')->with('success', '商品が出品されました。');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', '商品出品中にエラーが発生しました: ' . $e->getMessage());
        }
    }

    private function saveItem(Request $request)  
    {  
        $request->validate([  
            'item_name' => 'required|string|max:255',  
            'brand' => 'nullable|string|max:255',  
            'description' => 'required|string',  
            'price' => 'required|numeric',  
            'condition_id' => 'required|exists:conditions,id',  
            // 他のバリデーションを追加  
        ]);  

        return Item::create([  
            'name' => $request->item_name,  
            'brand' => $request->brand,  
            'description' => $request->description,  
            'price' => $request->price,  
            'condition_id' => $request->condition_id,  
            // 他のカラムを追加  
        ]);  
    }
//いいね機能
    public function like(Request $request, $id)  
    {  
        if (!Auth::check()) {  
            return redirect()->route('item.show', $id)->with('error', 'ログインが必要です。');  
        }  

        $like = Like::where('user_id', Auth::id())->where('item_id', $id)->first();  

        if ($like) {  
            // すでにいいねがある場合は削除  
            $like->delete();  
            return redirect()->route('item.show', $id)->with('status', 'いいねを解除しました。');  
        } else {  
            // いいねを新規追加  
            Like::create([  
                'user_id' => Auth::id(),  
                'item_id' => $id,  
            ]);  
            return redirect()->route('item.show', $id)->with('status', 'いいねしました。');  
        }  
    }

public function getLikes($id)  
{  
    // アイテムに対するいいねの数を取得  
    $likeCount = Like::where('item_id', $id)->count();  
    return response()->json(['likes' => $likeCount]);  
}  
}