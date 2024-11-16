<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function register(Request $request) {
        $form = $request->validated();

        // テキストで取得したitem_conditionを数値に変換
        $form['item_condition'] = array_search($form['item_condition'], Item::CONDITIONS);

        Item::create($form);

        return redirect()->route('index');
    }
}
