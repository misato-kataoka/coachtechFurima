<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Order;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Facades\Session;

class OrdersController extends Controller
{
    public function show($item_id)
    {
        // 商品情報を取得
        $item = Item::findOrFail($item_id);

        // 商品購入ページを表示
        return view('purchase', compact('item'));
    }

    public function store(PurchaseRequest $request)
    {
        $validatedData = $request->validated();

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

    public function createCheckoutSession(Request $request)
    {
        // Stripe APIのキー設定
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // リクエストから必要データを取得
        $paymentMethod = $request->input('payment_method');
        $itemPrice = $request->input('item_price');
        $itemName = $request->input('item_name');

        try {
            // Stripe Checkoutセッションを作成
            $session = StripeSession::create([
                'payment_method_types' => ['card', 'konbini'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $itemName,
                        ],
                        'unit_amount' => $itemPrice * 1,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('success.page'), // 成功時のURL
                'cancel_url' => route('cancel.page'),   // キャンセル時のURL
            ]);

        return response()->json(['sessionId' => $session->id]); // セッションIDを返す
        } catch (\Exception $e) {
            // エラー処理
            \Log::error('Stripe error: ' . $e->getMessage());
            return response()->json(['error' => '決済処理中にエラーが発生しました。'], 500);
        }return response()->json(['sessionId' => $session->id]); // セッションIDを返す
    }
}