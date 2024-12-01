<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Item;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
                'success_url' => route('purchase.success', ['item_id' => $item->id]),
                'cancel_url' => route('purchase.cancel', ['item_id' => $item->id]),
            ]);

            return redirect($session->url);
        } catch (Exception $e) {
                   Log::error('Stripeエラー: ' . $e->getMessage());
                   
            return redirect()->route('purchase.show', ['item_id' => $item->id])
                ->with('error', '決済中に問題が発生しました。再度お試しください。');
        }
    }
// 支払い成功
    public function success($item_id)
    {
        $item = Item::findOrFail($item_id);

        return redirect()->route('item.detail', ['item_id' => $item->id])
            ->with('success', '購入が完了しました！');
    }
// 支払いキャンセル
    public function cancel($item_id)
    {
        $item = Item::findOrFail($item_id);

        return redirect()->route('purchase.show', ['item_id' => $item->id])
            ->with('error', '購入がキャンセルされました。');
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
