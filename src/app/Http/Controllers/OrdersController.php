<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Order;

class OrdersController extends Controller
{
    public function show($item_id)
    {
        // 商品情報を取得
        $item = Item::findOrFail($item_id);

        // 商品購入ページを表示
        return view('purchase', compact('item'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:card,convenience',
            // 他のバリデーションルール
        ]);

        // Orderを保存
        $order = Order::create([
            'user_id' => auth()->id(),
            'item_id' => $request->item_id,
            'payment_method' => $request->payment_method,
            // 他の属性
        ]);

        // 支払い方法に応じて処理を分ける
        if ($request->payment_method === 'card') {
            return redirect()->route('payment.process', $order->id);
        } else {
            // コンビニ払いの処理をここに追加する
            return redirect()->route('convenience.payment', $order->id);
        }
    }
}