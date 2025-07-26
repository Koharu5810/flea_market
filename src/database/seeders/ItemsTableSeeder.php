<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Item;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $brands = ['Sony', 'Adidas', 'Nike', 'Louis Vuitton', 'SHARP', null];

        $items = [
            [
                'user_id' => 1,
                'name' => '腕時計',
                'price' => '15000',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'dummy/clock.jpg',
                'categories' => ['メンズ', 'ファッション', 'アクセサリー'],
            ],
            [
                'user_id' => 1,
                'name' => 'HDD',
                'price' => '5000',
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'dummy/HDD.jpg',
                'categories' => ['家電'],
            ],
            [
                'user_id' => 1,
                'name' => '玉ねぎ3束',
                'price' => '300',
                'description' => '新鮮な玉ねぎの3束セット',
                'image' => 'dummy/onion.jpg',
                'categories' => ['キッチン'],
            ],
            [
                'user_id' => 1,
                'name' => '革靴',
                'price' => '4000',
                'description' => 'クラシックなデザインの革靴',
                'image' => 'dummy/shoes.jpg',
                'categories' => ['ファッション', 'メンズ'],
            ],
            [
                'user_id' => 1,
                'name' => 'ノートPC',
                'price' => '45000',
                'description' => '高性能なノートパソコン',
                'image' => 'dummy/PC.jpg',
                'categories' => ['家電'],
            ],
            [
                'user_id' => 2,
                'name' => 'マイク',
                'price' => '8000',
                'description' => '高音質のレコーディング用マイク',
                'image' => 'dummy/mic.jpg',
                'categories' => ['家電'],
            ],
            [
                'user_id' => 2,
                'name' => 'ショルダーバッグ',
                'price' => '3500',
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'dummy/shoulder-bag.jpg',
                'categories' => ['ファッション', 'レディース'],
            ],
            [
                'user_id' => 2,
                'name' => 'タンブラー',
                'price' => '500',
                'description' => '使いやすいタンブラー',
                'image' => 'dummy/tumbler.jpg',
                'categories' => ['キッチン'],
            ],
            [
                'user_id' => 2,
                'name' => 'コーヒーミル',
                'price' => '4000',
                'description' => '手動のコーヒーミル',
                'image' => 'dummy/coffee-grinder.jpg',
                'categories' => ['キッチン', 'インテリア'],
            ],
            [
                'user_id' => 2,
                'name' => 'メイクセット',
                'price' => '2500',
                'description' => '便利なメイクアップセット',
                'image' => 'dummy/makeup-set.jpg',
                'categories' => ['コスメ', 'レディース', 'ファッション'],
            ],
        ];

        // 各アイテムに1〜4のコンディションを順番に挿入するための初期値設定
        $conditionId = 1;

        foreach ($items as $item) {
            $randomBrand = $brands[array_rand($brands)];

            // itemsテーブルへ挿入
            $createdItem = Item::create([
                'name' => $item['name'],
                'price' => $item['price'],
                'description' => $item['description'],
                'image' => $item['image'],
                'item_condition' => $conditionId,
                'user_id' => $item['user_id'],
                'address_id' => null,
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
