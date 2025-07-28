<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\ChatRoom;
use App\Models\UserAddress;

class ChatRoomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $purchaseData = [
            ['item_id' => 1, 'buyer_id' => 3],
            ['item_id' => 3, 'buyer_id' => 2],
            ['item_id' => 5, 'buyer_id' => 3],
        ];

        foreach ($purchaseData as $data) {
            $item = \App\Models\Item::find($data['item_id']);

            if (!$item) {
                continue; // 商品が見つからない場合はスキップ
            }

            // 購入者の住所取得
            $addressId = UserAddress::where('user_id', $data['buyer_id'])->value('id');

            // order作成または更新
            $order = Order::updateOrCreate(
                [
                    'item_id' => $data['item_id'],
                    'user_id' => $data['buyer_id'],
                ],
                [
                    'address_id' => $addressId,
                    'payment_method' => 'credit_card',
                    'purchased_at' => now(),
                    'status' => 'trading',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // チャットルーム作成
            ChatRoom::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
