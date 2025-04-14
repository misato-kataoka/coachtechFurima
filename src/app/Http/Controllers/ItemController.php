<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Http\Request;
use App\Models\Item;
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

    public function show($id)
    {
    $item = Item::findOrFail($id); // IDに基づいて商品を取得
    return view('item_detail', compact('item')); // item_detail ビューに渡す
    }

}