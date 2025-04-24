<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Http\Request;
use App\Models\Item;
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


    /*public function myList()  
    {  
        // セッションやデータベースから商品を取得する  
        $items = Item::where('user_id', auth()->id())->paginate(8);

        return view('mylist', compact('items'));  
    } */

    public function show($id)
    {
    $item = Item::with('comments.user')->findOrFail($id); // IDに基づいて商品を取得
    return view('item_detail', compact('item')); // item_detail ビューに渡す
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

    public function store(Request $request)  
    {  
        $request->validate([  
            'content' => 'required|string',  
            'item_id' => 'required|exists:items,id',  
        ]);  

        Comment::create([  
            'user_id' => auth()->id(),  
            'item_id' => $request->item_id,  
            'content' => $request->content,  
        ]);  

        return redirect()->back()->with('success', 'コメントが送信されました。');  
    }
}