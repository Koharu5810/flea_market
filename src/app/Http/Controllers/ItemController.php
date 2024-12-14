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
    public function index(Request $request) {
    // 購入成功処理
        if ($request->has('success') && $request->success == 'true' && $request->has('item_id')) {
            $item = Item::find($request->item_id);
            if ($item) {
                session()->flash('success', '購入が完了しました！');
            }
        }

        $tab = $request->query('tab');
        $query = $request->query('query');

        if ($tab === 'mylist') {
            if (auth()->check()) {
                // 認証ユーザはお気に入りリストを取得
                $user = auth()->user();
                $items = $user->favorites();

                if (!empty($query)) {
                    $items->where('name', 'like', '%' . $query . '%');
                }

                $items = $items->get();
            } else {
                // 未認証の場合は空を返す
                $items = collect();
            }
        } else {
            // 全商品一覧を取得
            $items = Item::where('user_id', '!=', Auth::id());

            if (!empty($query)) {
                $items->where('name', 'like', '%' . $query . '%');
            }

            $items = $items->get();
        }

        return view('home', compact('items', 'tab'));
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
    public function showDetail($item_id)
    {
        $item = Item::with(['categories', 'comments.user', 'favoriteBy'])->findOrFail($item_id);
        $user = auth()->check() ? auth()->user() : null; // 認証済みの場合のみユーザー情報を取得

        return view('item-detail', compact('item', 'user'));
    }
// お気に入り登録
    public function toggleFavorite($id)
    {
        if (!auth()->check()) {
            return redirect()->route('item.login');
        }

        $user = auth()->user();
        $item = Item::find($id);

        if (!$item) {
            return redirect()->back();
        }

        if ($item->favoriteBy()->where('user_id', $user->id)->exists()) {
            $item->favoriteBy()->detach($user->id);  // すでにお気に入りの場合usersテーブルから削除
        } else {
            $item->favoriteBy()->attach($user->id);  // お気に入りでない場合usersテーブルに追加
        }

        $item->refresh();

        return redirect()->back();
    }
// コメント送信フォーム
    public function commentStore(CommentRequest $request)
    {
        if (!auth()->check()) {
            return redirect()->route('item.login');
        }

        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $request->item_id,
            'comment' => $request->comment,
        ]);

        return redirect()->route('item.detail', ['item_id' => $request->item_id]);
    }

// 検索機能
    public function search(Request $request)
    {
        $items = Item::query();
        if ($request->filled('query')) {
            $items->where('name', 'like', '%' . $request->input('query') . '%');
        }

        $items = $items->get();
        $tab = 'search';

        return view('home', compact('items', 'tab'));
    }
}
