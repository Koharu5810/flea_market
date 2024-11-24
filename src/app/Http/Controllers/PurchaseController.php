<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\UserAddress;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\FuncCall;

class PurchaseController extends Controller
{
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);

        $address = UserAddress::where('user_id', auth()->id())->firstOrFail();

        return view('purchase.index', compact('item', 'address'));
    }

    public function showAddressForm($item_id)
    {
        $address = UserAddress::where('user_id', auth()->id())->firstOrFail();
        $item = Item::findOrFail($item_id);

        return view('purchase.address', compact('address', 'item'));
    }
    public function saveShippingAddress(Request $request, $item_id)
    {
        ShippingAddress::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building' => $request->building,
            ]
        );

        return redirect()->route('purchase.index', compact('address', 'item_id'));
    }
}
