<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Tests\Helpers\TestHelper;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    private function openLoginPage()
    {
        return $this->get(route('show.login'))
            ->assertStatus(200)
            ->assertSee('ログインする');
    }
    private function assertValidationError($data, $expectedErrors)
    {
        $response = $this->post(route('login'), $data);
        $response->assertStatus(302);
        $response->assertSessionHasErrors($expectedErrors);
    }

    public function test_email_validation_error_when_email_is_missing()
    {
        $this->openLoginPage();

        $data = [
            'username' => '',
            'password' => 'password123',
        ];
        $expectedErrors = ['username' => 'ユーザー名またはメールアドレスを入力してください'];

        $this->assertValidationError($data, $expectedErrors);
    }
    public function test_password_validation_error_when_password_is_missing()
    {
        $this->openLoginPage();

        $data = [
            'username' => 'TEST USER',
            'password' => '',
        ];
        $expectedErrors = ['password' => 'パスワードを入力してください'];

        $this->assertValidationError($data, $expectedErrors);
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
        $response->assertStatus(302)
            ->assertRedirect(route('login'))
            ->assertSessionHas('auth_error', 'ログイン情報が登録されていません');

        // ユーザーが認証されていないことを確認
        $this->assertGuest();
    }

// 全ての項目が正しく入力されている場合、ログイン処理実行
    public function test_user_can_login_and_redirect_to_home()
    {
        $user = TestHelper::userLogin();

        $data = [
            'username' => 'TEST USER',
            'password' => 'password123',
        ];

        $response = $this->post(route('login'), $data);
        $response->assertSessionDoesntHaveErrors();
        $response->assertStatus(302);

        // 認証ユーザであることを確認
        $this->assertAuthenticatedAs($user);
    }
}
