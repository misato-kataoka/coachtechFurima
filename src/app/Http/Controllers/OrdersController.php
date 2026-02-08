<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class OrdersController extends Controller
{
    public function show($item_id)
    {
        // 商品情報を取得
        $item = Item::findOrFail($item_id);

        // 商品購入ページを表示
        return view('purchase', compact('item'));
    }

    public function createCheckoutSession(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $item = Item::findOrFail($request->input('item_id'));
        $paymentMethodType = $request->input('payment_method');

        if ($item->is_sold) {
            return response()->json(['error' => 'この商品は売り切れです。'], 409);
        }

        try {
            $session = StripeSession::create([
                'payment_method_types' => [$paymentMethodType === 'convenience' ? 'konbini' : 'card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $item->item_name,
                        ],
                        'unit_amount' => (int) $item->price,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',

                'metadata' => [
                    'item_id' => $item->id,
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'payment_method_id' => $paymentMethodType,
                ],
                'success_url' => route('payment.success'),
                'cancel_url' => route('payment.cancel'),
            ]);

            return response()->json(['sessionId' => $session->id]);

        } catch (\Exception $e) {
            return response()->json(['error' => '決済セッションの作成に失敗しました。: ' . $e->getMessage()], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->server('HTTP_STRIPE_SIGNATURE');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET'); // .envに設定するWebhook署名キー

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\UnexpectedValueException | SignatureVerificationException $e) {
            return response('Invalid payload', 400);
        }

        // checkout.session.completed イベントを処理
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $metadata = $session->metadata;

            $item = Item::find($metadata->item_id);

            // 商品が存在し、まだ売れていない場合のみ処理
            if ($item && !$item->is_sold) {
                // 'card'などの名前からpayment_method_idを取得
                $paymentMethod = PaymentMethod::where('name', $metadata->payment_method_name)->first();

                // ★ここで初めてデータベースを更新する★
                $item->update([
                    'buyer_id' => $metadata->user_id,
                    'is_sold' => true,
                    'payment_method_id' => $paymentMethod ? $paymentMethod->id : null,
                ]);
            }
        }

        return response('Webhook Handled', 200);
    }

    public function success()
    {
        return view('success');
    }

    // キャンセルページ
    public function cancel()
    {
        return view('cancel');
    }
}