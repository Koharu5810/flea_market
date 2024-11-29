<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
// 住所変更画面の表示
    public function showAddressForm($item_id)
    {
        $user = Auth::user();
        $address = UserAddress::where('user_id', auth()->id())->firstOrFail();
        $item = Item::findOrFail($item_id);

        return view('purchase.address', compact('item', 'address'));
    }
// 住所変更
    public function saveShippingAddress(Request $request, $item_id)
    {
        UserAddress::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building' => $request->building,
            ]
        );

        return redirect()->route('redirectPurchase', compact('item_id'));
    }
}
