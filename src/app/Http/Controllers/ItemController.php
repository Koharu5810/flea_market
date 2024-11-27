<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
// マイページを表示
    public function index() {
        return view('home');
    }
// 商品出品画面の表示
    public function showSell()
    {
        $categories = Category::all();

        return view('sell', compact('categories'));
    }
// 商品出品
    public function createItem(ExhibitionRequest $request)
    {
        $validated = $request->validated();
        $validated['item_condition'] = (int) $validated['item_condition'];

        $item = new Item();
        $item->fill($validated);

        if ($request->hasFile('image')) {
            $item->image = $request->file('image')->store('items', 'public');
        }

        $item->user_id = auth()->id();  // ログインユーザのIDを設定
        $item->save();

        if (!empty($validated['category'])) {
            $item->categories()->sync($validated['category']);  // 中間テーブルに保存
        }

        return redirect()->route('home');
    }

// 商品詳細画面の表示
    public function showDetail($id)
    {
        $item = Item::with(['categories', 'comments.user', 'favoriteBy'])->findOrFail($id);
        $user = auth()->user();

        return view('item-detail', compact('item', 'user'));
    }
// お気に入り登録
    public function toggleFavorite(Request $request,$id)
    {
        $user = auth()->user();

        $item = Item::find($id);
        if (!$item) {
            return redirect()->back();
        }

        if ($item->favoriteBy()->where('user_id', $user->id)->exists()) {
            // すでにお気に入りの場合usersテーブルから削除
            $item->favoriteBy()->detach($user->id);
        } else {
            // お気に入りでない場合usersテーブルに追加
            $item->favoriteBy()->attach($user->id);
        }

        $item->refresh();

        return redirect()->back();
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
