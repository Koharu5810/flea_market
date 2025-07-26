<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Order::updateOrCreate(
            ['item_id' => 1],
            [
                'user_id' => 3,
                'item_id' => 1,
                'address_id' => User::find(3)->user_address->id,
                'payment_method' => 'credit_card',
                'purchased_at' => now(),
            ]
        );
    }
}
