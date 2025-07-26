<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\ChatRoom;

class ChatRoomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $order = Order::where('item_id', 1)->where('user_id', 3)->first();

        if ($order) {
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
