<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Http\Requests\ChatRequest;
use App\Mail\SellerRated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
// 特定のチャットルームを表示
    public function show(ChatRoom $chatRoom)
    {
        $this->authorize('view', $chatRoom);  // 出品者と購入者のみ表示可能

        $messages = $chatRoom->messages()->with('sender')->get();
        $order = $chatRoom->order;
        $item = $order->item;

        $user = auth()->user();
        $isSeller = $user->id === $item->user_id;   // 出品者
        $isBuyer = $user->id === $order->user->id;  // 購入者

        $opponent = $isBuyer ? $item->user : $order->user;

        // 購入者評価後、出品者に評価モーダルを表示
        $shouldShowRatingModal = $isSeller && $order->status === 'buyer_rated';

        return view('chat.trading_chat', compact(
            'chatRoom',
            'messages',
            'order',
            'item',
            'user',
            'opponent',
            'isSeller',
            'isBuyer',
            'shouldShowRatingModal',
        ));
    }

// メッセージ送信
    public function send(ChatRequest $request, ChatRoom $chatRoom)
    {
        $message = new Message();
        $message->chat_room_id = $chatRoom->id;
        $message->sender_id = auth()->id();
        $message->content = $request->input('content');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('chat_images', 'public');
            $message->image_path = $path;
        }

        $message->save();

        return back()->with('success', 'メッセージを送信しました');
    }

// チャット編集
    public function edit(ChatRoom $chatRoom, Message $message)
    {
        // セキュリティチェック
        if ($message->chat_room_id !== $chatRoom->id) {
            abort(403, '不正なアクセスです');
        }
        if ($message->sender_id !== auth()->id()) {
            abort(403, '自分のメッセージ以外は編集できません');
        }

        session()->put('edit_id', $message->id);

        return back();
    }
// 編集キャンセル
    public function cancelEdit(ChatRoom $chatRoom)
    {
        session()->forget('edit_id');
        return redirect()->route('chat.show', ['chatRoom' => $chatRoom]);
    }
// チャット編集後更新
    public function update(Request $request, ChatRoom $chatRoom, Message $message)
    {
        if ($message->chat_room_id !== $chatRoom->id || $message->sender_id !== auth()->id()) {
            abort(403);
        }

        if ($request->input('action') === 'cancel') {
            return redirect()->route('chat.show', ['chatRoom' => $chatRoom->id])
                            ->with('info', '編集をキャンセルしました');
        }

        // 通常の更新処理
        $message->content = $request->input('content');
        $message->save();

        // セッションから削除
        session()->forget('edit_id');

        return redirect()->route('chat.show', ['chatRoom' => $chatRoom->id])
                        ->with('success', 'メッセージを更新しました');
    }

// チャット削除
    public function delete(ChatRoom $chatRoom, Message $message)
    {
        if ($message->chat_room_id !== $chatRoom->id) {
            abort(403, '不正なアクセスです');
        }
        if ($message->sender_id !== auth()->id()) {
            abort(403, '自分のメッセージ以外は削除できません');
        }

        $message->delete();

        return back()->with('success', 'メッセージを削除しました');
    }

// 取引完了（購入者）
    public function buyerRate(Request $request, ChatRoom $chatRoom)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ], [
            'rating.required' => '★1以上で評価してください',
        ]);

        $order = $chatRoom->order;

        if (auth()->id() !== optional($order->user)->id) {
            abort(403);
        }
        if (!is_null($order->rated_at)) {
            return back()->with('error', 'この取引はすでに評価されています');
        }

        // 取引完了
        $order->status = 'buyer_rated';

        // 出品者に評価を加算
        $seller = $order->item->user;
        $seller->rating_total += $request->rating;
        $seller->rating_count += 1;
        $seller->save();

        // 注文に評価記録
        $order->rating = $request->rating;
        $order->rated_at = now();
        $order->save();

        // 出品者へ取引完了メール送信
        try {
            Mail::to($seller->email)->send(new SellerRated($order, $request->rating));
        } catch (\Exception $e) {
            Log::error('評価通知メール送信失敗: ' . $e->getMessage());
        }

        return redirect()->route('home')->with('success', '出品者を評価しました');
    }
// 取引完了（出品者）
    public function sellerRate(Request $request, ChatRoom $chatRoom)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ], [
            'rating.required' => '★1以上で評価してください',
        ]);

        $order = $chatRoom->order;

        // 出品者かどうか確認
        if (auth()->id() !== optional($order->item)->user_id) {
            abort(403);
        }

        // 二重評価防止（statusがbuyer_ratedのときのみ評価可能）
        if ($order->status !== 'buyer_rated') {
            return back()->with('error', '評価できる状態ではありません。');
        }

        // 購入者に評価を加算
        $buyer = $order->user;
        $buyer->rating_total += $request->rating;
        $buyer->rating_count += 1;
        $buyer->save();

        // ステータスを「取引完了」に更新
        $order->status = 'complete';
        $order->save();

        return redirect()->route('home')->with('success', '購入者を評価しました。');
    }

}
