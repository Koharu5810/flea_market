<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // CSRF トークン検証を無効化
        $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    }

    public function test_email_validation_error_when_email_is_missing()
    {
        $url = route('login');

        $data = [
            'username' => '',
            'password' => 'password123',
        ];

        $response = $this->postJson($url, $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'username' => 'ユーザー名またはメールアドレスを入力してください',
        ]);
    }
    public function test_password_validation_error_when_password_is_missing()
    {
        $url = route('register');

        $data = [
            'username' => 'TEST USER',
            'password' => '',
        ];

        $response = $this->postJson($url, $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    // 全ての項目が正しく入力されている場合、ログイン処理実行
    public function test_user_can_login_and_redirect_to_home()
    {
        $url = route('login');

        // 事前にユーザーを作成
        $user = User::factory()->create([
            'username' => 'TEST USER',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $data = [
            'username' => 'TEST USER',
            'password' => 'password123',
        ];

        $response = $this->post($url, $data);
        $response->assertStatus(302);   // ステータスコード302を確認（リダイレクト）
        $response->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }
}
