<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Item;

class FavoritesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $items = Item::all();

        // 各ユーザーがランダムにお気に入りアイテムを登録
        foreach ($users as $user) {
            // ユーザーごとに最大5個のアイテムをお気に入りに追加
            $favoriteItems = $items->random(rand(1, 5));

            foreach ($favoriteItems as $item) {
                DB::table('favorites')->insert([
                    'user_id' => $user->id,
                    'item_id' => $item->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
