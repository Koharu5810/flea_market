<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 中間テーブルに挿入するための設定
        $conditions = [
            '良好' => 1,
            '目立った傷や汚れなし' => 2,
            'やや傷や汚れあり' => 3,
            '状態が悪い' => 4,
        ];

        $items = [
            [
                'name' => '腕時計',
                'price' => '15,000',
                'description' => '',
                'image' => '',
                'conditions' => '',
            ],
            [
                'name' => 'HDD',
                'price' => '5,000',
                'description' => '',
                'image' => '',
                'conditions' => '',
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => '300',
                'description' => '',
                'image' => '',
                'conditions' => '',
            ],
            [
                'name' => '革靴',
                'price' => '4,000',
                'description' => '',
                'image' => '',
                'conditions' => '',
            ],
            [
                'name' => 'ノートPC',
                'price' => '45,000',
                'description' => '',
                'image' => '',
                'conditions' => '',
            ],
            [
                'name' => 'マイク',
                'price' => '8,000',
                'description' => '',
                'image' => '',
                'conditions' => '',
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => '3,500',
                'description' => '',
                'image' => '',
                'conditions' => '',
            ],
            [
                'name' => 'タンブラー',
                'price' => '500',
                'description' => '',
                'image' => '',
                'conditions' => '',
            ],
            [
                'name' => 'コーヒーミル',
                'price' => '4,000',
                'description' => '',
                'image' => '',
                'conditions' => '',
            ],
            [
                'name' => 'メイクセット',
                'price' => '2,500',
                'description' => '',
                'image' => '',
                'conditions' => '',
            ],
        ];
    }
}
