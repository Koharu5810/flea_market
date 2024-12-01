<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\UserAddress;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userAddresses = UserAddress::all();
        $brands = ['Sony', 'Adidas', 'Nike', 'Louis Vuitton', 'SHARP', null];

        $items = [
            [
                'name' => '腕時計',
                'price' => '15000',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'images/clock.jpg',
                'categories' => ['メンズ', 'ファッション', 'アクセサリー'],
            ],
            [
                'name' => 'HDD',
                'price' => '5000',
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'images/HDD.jpg',
                'categories' => ['家電'],
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => '300',
                'description' => '新鮮な玉ねぎの3束セット',
                'image' => 'images/onion.jpg',
                'categories' => ['キッチン'],
            ],
            [
                'name' => '革靴',
                'price' => '4000',
                'description' => 'クラシックなデザインの革靴',
                'image' => 'images/shoes.jpg',
                'categories' => ['ファッション', 'メンズ'],
            ],
            [
                'name' => 'ノートPC',
                'price' => '45000',
                'description' => '高性能なノートパソコン',
                'image' => 'images/PC.jpg',
                'categories' => ['家電'],
            ],
            [
                'name' => 'マイク',
                'price' => '8000',
                'description' => '高音質のレコーディング用マイク',
                'image' => 'images/mic.jpg',
                'categories' => ['家電'],
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => '3500',
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'images/shoulder-bag.jpg',
                'categories' => ['ファッション', 'レディース'],
            ],
            [
                'name' => 'タンブラー',
                'price' => '500',
                'description' => '使いやすいタンブラー',
                'image' => 'images/tumbler.jpg',
                'categories' => ['キッチン'],
            ],
            [
                'name' => 'コーヒーミル',
                'price' => '4000',
                'description' => '手動のコーヒーミル',
                'image' => 'images/coffee-grinder.jpg',
                'categories' => ['キッチン', 'インテリア'],
            ],
            [
                'name' => 'メイクセット',
                'price' => '2500',
                'description' => '便利なメイクアップセット',
                'image' => 'images/makeup-set.jpg',
                'categories' => ['コスメ', 'レディース', 'ファッション'],
            ],
        ];

        // 各アイテムに1〜4のコンディションを順番に挿入するための初期値設定
        $conditionId = 1;

        foreach ($items as $item) {
            $randomAddress = $userAddresses->random();
            $randomBrand = $brands[array_rand($brands)];

            // itemsテーブルへ挿入
            $createdItem = Item::create([
                'name' => $item['name'],
                'price' => $item['price'],
                'description' => $item['description'],
                'image' => $item['image'],
                'item_condition' => $conditionId,
                'user_id' => $randomAddress->user_id,
                'address_id' => $randomAddress->id,
                'brand' => $randomBrand,
            ]);

            // アイテムのコンディションIDを設定（1〜4でローテーション）
            $conditionId = $conditionId < 4 ? $conditionId + 1 : 1;

            foreach ($item['categories'] as $category) {
                $categoryId = DB::table('categories')->where('content', $category)->value('id');

                // 中間テーブルへ挿入
                if ($categoryId) {
                    $createdItem->categories()->attach($categoryId);
                }
            }
        }
    }
}
