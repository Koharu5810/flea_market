<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\Item;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\PurchaseAddressRequest;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use PhpParser\Node\Expr\FuncCall;

class PurchaseController extends Controller
{
// 商品購入画面の表示
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);

        $address = UserAddress::where('user_id', auth()->id())->firstOrFail();

        return view('purchase.index', compact('item', 'address'));
    }
// 商品購入
    public function checkout(PurchaseRequest $request)
    {
        $user = auth()->user();
        $item = Item::findOrFail($request->item_id);

        // 一度購入された商品かどうかを確認
        if ($item->is_sold) {
            return redirect()->route('purchase.show', ['item_id' => $item->id])
                ->with('error', 'この商品は既に購入されています。');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $session = Session::create([
                'payment_method_types' => $request->payment_method === 'コンビニ支払い' ? ['konbini'] : ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'jpy',
                            'product_data' => [
                                'name' => $item->name,
                            ],
                            'unit_amount' => $item->price,
                        ],
                        'quantity' => 1,  // 注文数
                    ],
                ],
                'mode' => 'payment',
                'success_url' => route('home') . '?success=true&item_id=' . $item->id,
                'cancel_url' => route('purchase.show', ['item_id' => $item->id]),
            ]);

            return redirect($session->url);
        } catch (Exception $e) {
            return redirect()->route('purchase.show', ['item_id' => $item->id])
                ->with('error', '購入がキャンセルされました。');
        }
    }

// StripeのWebhooksで決済完了後の処理を実装
public function handleStripeWebhook(Request $request)
{
    $payload = $request->getContent();
    $sigHeader = $request->header('Stripe-Signature');
    $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

    try {
        $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
    } catch (\UnexpectedValueException $e) {
        // 無効なペイロード
        return response('Invalid payload', 400);
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        // シグネチャの検証失敗
        return response('Invalid signature', 400);
    }

    if ($event->type === 'checkout.session.completed') {
        $session = $event->data->object;

        // アイテムとユーザーを特定
        $itemId = $session->metadata->item_id;
        $userId = $session->metadata->user_id;

        $item = Item::find($itemId);
        $user = User::find($userId);

        if ($item && $user) {
            // Ordersテーブルにデータを挿入
            Order::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $user->id,
                'item_id' => $item->id,
                'address_id' => $user->address_id,
                'payment_method' => $session->payment_method_types[0],
                'purchased_at' => now(),
            ]);

            // アイテムの販売状態を更新
            $item->update(['is_sold' => true]);
        }
    }

    return response('Webhook handled', 200);
}

// 住所変更画面の表示
    public function editAddress($item_id)
    {
        $user = Auth::user();
        $address = UserAddress::where('user_id', auth()->id())->firstOrFail();
        $item = Item::findOrFail($item_id);

        return view('purchase.address', compact('item', 'address'));
    }
// 住所変更
    public function updateAddress(PurchaseAddressRequest $request, $item_id)
    {
        UserAddress::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building' => $request->building,
            ]
        );

        return redirect()->route('purchase.show', compact('item_id'));
    }
}
