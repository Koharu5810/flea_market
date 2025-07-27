<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Http\Requests\ChatRequest;


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

        return view('chat.trading_chat', compact('chatRoom', 'messages', 'order', 'item', 'user', 'opponent', 'isSeller', 'isBuyer'));
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
}
