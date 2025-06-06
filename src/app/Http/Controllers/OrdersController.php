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

        $item = Item::findOrFail($request->item_id);

        if ($item->is_sold) {
            return redirect()->back()->with('error', 'この商品はすでに購入されています。');
        }
        // Orderを保存
        $order = Order::create([
            'user_id' => auth()->id(),
            'item_id' => $request->item_id,
            'payment_method' => $request->payment_method,
            // 他の属性
        ]);

        // 商品の状態を更新
        $item->is_sold = true; // soldに設定
        $item->buyer_id = auth()->id(); // 購入者のIDを保存
        $item->save();

        // 支払い方法に応じて処理を分ける
        if ($request->payment_method === 'card') {
            return redirect()->route('create.checkout.session', ['item_id' => $order->item_id, 'payment_method' => $request->payment_method]);
            //return redirect()->route('payment.process', $order->id);
        } else {
            // コンビニ払いの処理をここに追加する
            return redirect()->route('convenience.payment', $order->id);
        }
    }

    public function createCheckoutSession(Request $request)
    {
        // Stripe APIのキー設定
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        // リクエストから必要データを取得
        $itemId = $request->input('item_id');
        $item = Item::findOrFail($itemId);
        $paymentMethod = $request->input('payment_method');
        //$itemPrice = $request->input('item_price');
        //$itemName = $request->input('item_name');

        try {
            // Stripe Checkoutセッションを作成
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card', 'konbini'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $item->item_name,
                            'metadata' => [
                                'item_id' => $item->id,
                            ],
                        ],
                        'unit_amount' => $item->price * 1,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('success.page') . '?session_id={CHECKOUT_SESSION_ID}', // 成功時のURL
                'cancel_url' => route('cancel.page'),   // キャンセル時のURL
            ]);

        return response()->json(['sessionId' => $session->id]); // セッションIDを返す
        } catch (\Exception $e) {
            // エラー処理
            \Log::error('Stripe error: ' . $e->getMessage());
            return response()->json(['error' => '決済処理中にエラーが発生しました。'], 500);
        }
    }

    public function success(Request $request)
{
    $sessionId = $request->query('session_id');

    try {
        // セッション情報を取得
        $session = \Stripe\Checkout\Session::retrieve($sessionId);

        $itemId = $session->metadata->item_id; // メタデータからitem_idを取得
        $item = Item::findOrFail($itemId);

        // 商品を更新
        $item->is_sold = true;
        $item->buyer_id = auth()->id(); // 購入者のIDを追加
        $item->payment_method = $session->payment_method_types[0];
        $item->save();

        // ここに完了メッセージを返すか、ビューを表示する
        return view('success', ['item' =>$item]);

    } catch (\Exception $e) {
        \Log::error('Error retrieving Stripe session: ' . $e->getMessage());
        return redirect()->route('error.page')->with('error', '決済処理中のエラーが発生しました。');
    }
}
}