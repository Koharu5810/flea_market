<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LogoutTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    public function test_authUser_can_logout_and_redirect_to_home()
    {
        $url = route('home');

        /** @var \App\Models\User $user */   // $userの型解析ツールエラーが出るため追記
        $user = User::factory()->create([
            'username' => 'TEST USER',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->assertNotNull($user);
        // ユーザをログイン状態にする
        $this->actingAs($user);

        $response = $this->get(route('home'));
        $response->assertSee('ログアウト');
        $response->assertStatus(200);

        $response = $this->post(route('logout'));
        $response->assertStatus(302);
        $response->assertRedirect($url);

        $this->assertGuest();
    }
}
