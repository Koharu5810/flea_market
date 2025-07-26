<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ChatRoom;
use App\Models\Message;

class ChatController extends Controller
{
// 特定のチャットルームを表示
    public function show(ChatRoom $chatRoom)
    {
        $this->authorize('view', $chatRoom);  // 出品者 or 購入者のみ
        $hideOnPages = true;  // ヘッダーボタン類非表示フラグ

        $messages = $chatRoom->messages()->with('sender')->get();
        $order = $chatRoom->order;
        $item = $order->item;

        $user = auth()->user();
        $opponent = $order->user_id === $user->id ? $item->user : $order->user;

        return view('chat.trading_chat', compact('chatRoom', 'hideOnPages', 'messages', 'order', 'item', 'user', 'opponent'));
    }

// メッセージ送信
    public function send(Request $request, ChatRoom $chatRoom)
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
}
