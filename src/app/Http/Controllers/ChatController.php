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

        // セッションに編集中のIDを保存して戻る（再表示時にフォームが切り替わる）
        // session(['edit_id' => $message->id]);
        session()->put('edit_id', $message->id);

        return back();
        // return view('chat.edit_message', compact('chatRoom', 'message'));
    }
    public function cancelEdit(ChatRoom $chatRoom)
    {
        session()->forget('edit_id');
        return back();
    }
    public function update(Request $request, ChatRoom $chatRoom, Message $message)
    {
        if ($message->chat_room_id !== $chatRoom->id || $message->sender_id !== auth()->id()) {
            abort(403);
        }

        // $message->content = $request->input('content');
        // $message->save();

    if ($request->input('action') === 'cancel') {
        return redirect()->route('chat.show', ['chatRoom' => $chatRoom->id])
                        ->with('info', '編集をキャンセルしました');
    }

    // 通常の更新処理
    $message->content = $request->input('content');
    $message->save();


        // セッションから削除
        session()->forget('edit_id');

        // return back()->with('success', 'メッセージを更新しました');
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
