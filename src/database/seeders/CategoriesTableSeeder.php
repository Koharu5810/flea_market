<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            'ファッション',
            '家電',
            'インテリア',
            'レディース',
            'メンズ',
            'コスメ',
            '本',
            'ゲーム',
            'スポーツ',
            'キッチン',
            'ハンドメイド',
            'アクセサリー',
            'おもちゃ',
            'ベビー・キッズ',
        ];

        // categoriesテーブルへ挿入（重複を避けるため既存チェックを追加している）
        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert([
                ['content' => $category],  // 確認条件
                ['content' => $category]   // 挿入内容
            ]);
        };
    }
}
