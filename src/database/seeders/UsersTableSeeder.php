<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserAddress;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ユーザーとその住所を生成
        $users=[
            ['id' => 1, 'username' => '西 怜奈', 'email' => 'reina.n@coachtech.com'],
            ['id' => 2, 'username' => '山田 太郎', 'email' => 'taro.y@coachtech.com'],
            ['id' => 3, 'username' => '増田 一世', 'email' => 'issei.m@coachtech.com'],
        ];

        foreach ($users as $user) {
            $createdUser= User::updateOrCreate(
                ['id' => $user['id']],
                [
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'email_verified_at' => now(),
                    'password' => 'password',
                ],
            );

            $createdUser->user_address()->updateOrCreate([], [
                'postal_code' => '100-000' . $user['id'],
                'address' => '東京都千代田区テスト町' . $user['id'] . '-1',
                'building' => 'テストビル' . $user['id'],
            ]);
        }
    }
}
