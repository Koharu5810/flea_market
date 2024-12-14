<?php

namespace Tests\Helpers;

use App\Models\User;

class TestHelper
{
    public static function userLogin()
    {
        // 事前にユーザーを作成
        $user = User::factory()->create([
            'username' => 'TEST USER',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        /** @var \App\Models\User $user */   // $userの型解析ツールエラーが出るため追記
        auth()->login($user);

        return $user;
    }
}
