<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserAddress;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ユーザーとその住所を生成
        User::factory(10)
            ->has(
                \App\Models\UserAddress::factory()->count(1), // 1つの住所を関連付け
                'user_address'
            )
            ->create();
    }
}
