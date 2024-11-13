<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // // 中間テーブルに挿入するための設定
        // $conditions = [
        //     '良好' => 1,
        //     '目立った傷や汚れなし' => 2,
        //     'やや傷や汚れあり' => 3,
        //     '状態が悪い' => 4,
        // ];

        $items = [
            [
                'name' => '腕時計',
                'price' => '15000',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'storage/images/clock.jpg',
                'categories' => ['メンズ', 'ファッション', 'アクセサリー'],
            ],
            [
                'name' => 'HDD',
                'price' => '5000',
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'storage/images/HDD.jpg',
                'categories' => ['家電'],
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => '300',
                'description' => '新鮮な玉ねぎの3束セット',
                'image' => 'storage/images/onion.jpg',
                'categories' => ['キッチン'],
            ],
            [
                'name' => '革靴',
                'price' => '4000',
                'description' => 'クラシックなデザインの革靴',
                'image' => 'storage/images/shoes.jpg',
                'categories' => ['ファッション', 'メンズ'],
            ],
            [
                'name' => 'ノートPC',
                'price' => '45000',
                'description' => '高性能なノートパソコン',
                'image' => 'storage/images/PC.jpg',
                'categories' => ['家電'],
            ],
            [
                'name' => 'マイク',
                'price' => '8000',
                'description' => '高音質のレコーディング用マイク',
                'image' => 'storage/images/mic.jpg',
                'categories' => ['家電'],
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => '3500',
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'storage/images/shoulder-bag.jpg',
                'categories' => ['ファッション', 'レディース'],
            ],
            [
                'name' => 'タンブラー',
                'price' => '500',
                'description' => '使いやすいタンブラー',
                'image' => 'storage/images/tumbler.jpg',
                'categories' => ['キッチン'],
            ],
            [
                'name' => 'コーヒーミル',
                'price' => '4000',
                'description' => '手動のコーヒーミル',
                'image' => 'storage/images/coffee-grinder.jpg',
                'categories' => ['キッチン', 'インテリア'],
            ],
            [
                'name' => 'メイクセット',
                'price' => '2500',
                'description' => '便利なメイクアップセット',
                'image' => 'storage/images/makeup-set.jpg',
                'categories' => ['コスメ', 'レディース', 'ファッション'],
            ],
        ];

        // 中間テーブルに挿入する際、各アイテムに1〜4のコンディションを順番に挿入するための初期値設定
        $conditionId = 1;

        foreach ($items as $item) {
            // itemsテーブルへ挿入
            $itemId = DB::table('items')->insertGetId([
                'name' => $item['name'],
                'price' => $item['price'],
                'description' => $item['description'],
                'image' => $item['image'],
                'condition_id' => $conditionId,
            ]);

            // アイテムのコンディションIDを設定（1〜4でローテーション）
            $conditionId = $conditionId < 4 ? $conditionId + 1 : 1;

            foreach ($item['categories'] as $category) {
                $categoryId = DB::table('categories')->where('content', $category)->value('id');

                // 中間テーブルへ挿入
                if ($categoryId) {
                    DB::table('item_category')->insert([
                        'item_id' => $itemId,
                        'category_id' => $categoryId,
                    ]);
                }
            }
        }
    }
}
