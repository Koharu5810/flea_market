<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Message;
use App\Models\ChatRoom;

class MessagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $messages = [
            '購入ありがとうございます！',
            '短い間ですがよろしくお願いします',
            '入金が確認できたので、発送します！',
        ];

        ChatRoom::with('order.item')->each(function ($chatRoom) use ($messages) {
            $order = $chatRoom->order;
            $buyerId = $order->user_id;
            $sellerId = $order->item->user_id;

            foreach ($messages as $index => $content) {
                // 奇数: 出品者, 偶数: 購入者が送信
                $senderId = $index % 2 === 0 ? $sellerId : $buyerId;

                Message::create([
                    'chat_room_id' => $chatRoom->id,
                    'sender_id' => $senderId,
                    'content' => $content,
                    'created_at' => now()->addMinutes($index * 2),
                    'updated_at' => now()->addMinutes($index * 2),
                ]);
            }
        });
    }
}
