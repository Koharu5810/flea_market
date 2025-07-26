<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Order;
use App\Models\Message;
use App\Models\ChatRoom;
use Carbon\Carbon;

class MessagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $itemMessages = [
            1 => [
                ['sender' => 'seller', 'content' => 'ご購入ありがとうございます！'],
                ['sender' => 'buyer', 'content' => '短い間ですがよろしくお願いします'],
                ['sender' => 'seller', 'content' => '入金が確認できたので、発送します！'],
            ],
            3 => [
                ['sender' => 'seller', 'content' => 'ご購入ありがとうございます！'],
                ['sender' => 'buyer', 'content' => 'スムーズにお取引できればと思います'],
            ],
            5 => [
                ['sender' => 'seller', 'content' => 'ご購入ありがとうございます！'],
                ['sender' => 'seller', 'content' => '発送準備に入ります'],
                ['sender' => 'seller', 'content' => '追跡番号は1234-5678です'],
                ['sender' => 'seller', 'content' => '何かあればご連絡ください'],
            ],
        ];

        foreach ($itemMessages as $itemId => $messages) {
            $item = Item::find($itemId);
            if (!$item) continue;

            $order = Order::where('item_id', $itemId)->first();
            if (!$order) continue;

            $sellerId = $item->user_id;
            $buyerId = $order->user_id;

            $chatRoom = ChatRoom::where('order_id', $order->id)->first();
            if (!$chatRoom) continue;

            // チャットメッセージ作成時間のためのベース時間
            $baseTime = Carbon::now()->subDays(rand(0, 3))->setTime(rand(9, 18), rand(0, 59));

            foreach ($messages as $index => $message) {
                $senderId = $message['sender'] === 'seller' ? $sellerId : $buyerId;

                // メッセージごとに5〜15分ずつ加算
                $messageTime = (clone $baseTime)->addMinutes(rand(5, 15) * $index);

                Message::create([
                    'chat_room_id' => $chatRoom->id,
                    'sender_id' => $senderId,
                    'content' => $message['content'],
                    'is_read' => false,
                    'created_at' => $messageTime,
                    'updated_at' => $messageTime,
                ]);
            }
        }
    }
}
