<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Order;
use App\Models\Item;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\PurchaseAddressRequest;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PurchaseController extends Controller
{
// 商品購入画面の表示
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);
        $address = UserAddress::where('user_id', auth()->id())->first();

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
                'success_url' => url('/purchase/' . $item->id . '/success?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => route('purchase.show', ['item_id' => $item->id]),
            ]);

            return redirect($session->url);
        } catch (Exception $e) {
            return redirect()->route('purchase.show', ['item_id' => $item->id])
                ->with('error', '購入がキャンセルされました。');
        }
    }
// 購入完了後の処理
    public function success(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $sessionId = $request->session_id;
        $itemId = $request->item_id;

        $session = \Stripe\Checkout\Session::retrieve($sessionId);

        if ($session->payment_status === 'paid') {
            $item = Item::findOrFail($itemId);

            // 商品が既に売れていないか確認
            if ($item->is_sold) {
                return redirect()->route('home')->with('error', 'この商品は既に購入されています。');
            }

            // トランザクションを使用することで処理が失敗した場合ロールバック
            DB::transaction(function () use ($item, $session) {
                $address = auth()->user()->user_address;

                if (!$address) {
                    return redirect()->route('profile.edit')->with('error', '配送先住所を登録してください。');
                }

                $addressId = $address->id;

                // Ordersテーブルにデータを挿入
                Order::create([
                    'uuid' => (string) Str::uuid(),
                    'user_id' => auth()->id(),
                    'item_id' => $item->id,
                    'address_id' => $addressId,
                    'payment_method' => $session->payment_method_types[0] ?? 'unknown',
                    'purchased_at' => now(),
                ]);

                // 商品を販売済みに更新
                $item->update([
                    'is_sold' => true,
                    'address_id' => $addressId,  // 購入者の住所をitemsテーブルに挿入
                ]);
            });

            return redirect()->route('home')->with('success', '購入が完了しました。');
        }

        return redirect()->route('home')->with('error', '決済に失敗しました。');
    }

// 住所変更画面の表示
    public function editAddress($item_id)
    {
        $user = Auth::user();
        $address = UserAddress::where('user_id', auth()->id())->first();
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
