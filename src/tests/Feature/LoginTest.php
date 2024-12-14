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

    public function openLoginPage()
    {
        $response = $this->get(route('show.login'));
        $response->assertStatus(200);
        $response->assertSee('ログインする');

        return $response;
    }

    public function test_email_validation_error_when_email_is_missing()
    {
        $this->openLoginPage();

        $data = [
            'username' => '',
            'password' => 'password123',
        ];

        $response = $this->post(route('login'), $data);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'username' => 'ユーザー名またはメールアドレスを入力してください',
        ]);
    }
    public function test_password_validation_error_when_password_is_missing()
    {
        $data = [
            'username' => 'TEST USER',
            'password' => '',
        ];

        $response = $this->post(route('login'), $data);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }
    public function test_login_fails_with_invalid_credentials()
    {
        $this->openLoginPage();

        User::factory()->create([
            'username' => 'TEST USER',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $data = [
            'username' => 'wronguser',
            'password' => 'wrongpassword',
        ];

        $response = $this->post(route('login'), $data);
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('auth_error', 'ログイン情報が登録されていません');

        // ユーザーが認証されていないことを確認
        $this->assertGuest();
    }

// 全ての項目が正しく入力されている場合、ログイン処理実行
    public function test_user_can_login_and_redirect_to_home()
    {
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

        $response = $this->post(route('login'), $data);
        $response->assertStatus(302);
        $response->assertRedirect(route('home'));

        // 認証ユーザであることを確認
        $this->assertAuthenticatedAs($user);
    }
}
