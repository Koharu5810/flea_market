<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
// マイページを表示
    public function index() {
        return view('home');
    }
// 商品詳細画面の表示
    public function show($id)
    {
        $item = Item::with(['categories', 'comments.user'])->findOrFail($id);

        return view('item-detail', compact('item'));
    }

    public function register(Request $request) {
        $form = $request->validated();

        // テキストで取得したitem_conditionを数値に変換
        $form['item_condition'] = array_search($form['item_condition'], Item::CONDITIONS);

        Item::create($form);

        return redirect()->route('index');
    }
// コメント送信フォーム
    public function commentStore(CommentRequest $request)
    {
        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $request->item_id,
            'comment' => $request->comment,
        ]);

        return redirect()->route('item.detail', ['id' => $request->item_id]);
    }
}
