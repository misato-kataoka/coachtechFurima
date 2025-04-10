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
        $items = Item::paginate(8); // 1ページに8件の商品
        return view('item_list', compact('items'));
    }

    public function show($id)
    {
    $item = Item::findOrFail($id); // IDに基づいて商品を取得
    return view('item_detail', compact('item')); // item_detail ビューに渡す
    }
}