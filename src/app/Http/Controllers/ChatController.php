<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatRoom;
use App\Models\Message;

class ChatController extends Controller
{
// 特定のチャットルームを表示
    public function show(ChatRoom $chatRoom)
    {
        $this->authorize('view', $chatRoom); // 出品者 or 購入者のみ
        $messages = $chatRoom->messages()->with('sender')->get();

        return view('chat.show', compact('chatRoom', 'messages'));
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
