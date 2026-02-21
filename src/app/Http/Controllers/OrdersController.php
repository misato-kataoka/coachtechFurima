<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\User;
use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
                'success_url' => route('payment.success', ['item_id' => $item->id]),
                'cancel_url' => route('payment.cancel', ['item_id' => $item->id]),
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
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        $event = null;

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\UnexpectedValueException $e) {

            Log::error('Stripe Webhook: Invalid payload.', ['exception' => $e]);
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {

            Log::error('Stripe Webhook: Invalid signature.', ['exception' => $e]);
            return response('Invalid signature', 400);
        }

        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object; 
            $metadata = $session->metadata;

            // metadataから保存したIDを取得
            $itemId = $metadata->item_id ?? null;
            $userId = $metadata->user_id ?? null;
            $paymentMethodId = $metadata->payment_method_id ?? 1;

            // IDが見つからない場合は処理を終了
            if (!$itemId || !$userId) {
                Log::error('Stripe Webhook: Missing metadata.', ['metadata' => $metadata]);
                return response('Webhook Handled with missing metadata', 200);
            }

            try {
                DB::transaction(function () use ($itemId, $userId, $paymentMethodId) {
                    $item = Item::lockForUpdate()->find($itemId);
                    $user = User::find($userId);

                    if ($item && $user && is_null($item->buyer_id)) {
                        Order::create([
                            'user_id' => $user->id,
                            'item_id' => $item->id,
                            'payment_method_id' => $paymentMethodId,
                            'amount' => $item->price,
                            'shipping_post_code' => $user->post_code,
                            'shipping_address' => $user->address,
                            'shipping_building' => $user->building,
                        ]);

                        $item->update([
                            'buyer_id' => $user->id,
                        ]);
                    }
                });
            } catch (\Exception $e) {
                // DB更新中にエラーが発生した場合
                Log::error('Stripe Webhook: Database update failed.', ['exception' => $e, 'metadata' => $metadata]);
                return response('Database error', 500);
            }
        }

        // Stripeに処理が正常に完了したことを通知
        return response('Webhook Handled', 200);
    }

    public function success(Request $request)
    {
        $itemId = $request->query('item_id');
        
        $item = $itemId ? Item::find($itemId) : null;

        return view('success', ['item' => $item]);
    }

    // キャンセルページ
    public function cancel()
    {
        return view('cancel');
    }
}