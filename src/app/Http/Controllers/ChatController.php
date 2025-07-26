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
    public function store(Request $request, ChatRoom $chatRoom)
    {
        $this->authorize('view', $chatRoom);

        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $chatRoom->messages()->create([
            'sender_id' => auth()->id(),
            'content'   => $request->content,
        ]);

        return back();
    }
}
