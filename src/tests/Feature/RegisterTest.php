<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // CSRF トークン検証を無効化
        $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    }

    public function test_username_validation_error_when_username_is_missing()
    {
        $url = route('register');

        $data = [
            'username' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson($url, $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'username' => 'お名前を入力してください',
        ]);
    }
    public function test_email_validation_error_when_email_is_missing()
    {
        $url = route('register');

        $data = [
            'username' => 'TEST USER',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson($url, $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }
    public function test_password_validation_error_when_password_is_missing()
    {
        $url = route('register');

        $data = [
            'username' => 'TEST USER',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson($url, $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }
    public function test_password_validation_error_when_password_is_too_short()
    {
        $url = route('register');

        $data = [
            'username' => 'TEST USER',
            'email' => 'test@example.com',
            'password' => 'short12',
            'password_confirmation' => 'short12',
        ];

        $response = $this->postJson($url, $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
    }
    public function test_password_validation_error_when_password_confirmation_does_not_match()
    {
        $url = route('register');

        $data = [
            'username' => 'TEST USER',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password456',
        ];

        $response = $this->postJson($url, $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'password_confirmation' => 'パスワードと一致しません',
        ]);
    }

// 全ての項目が入力されている場合、会員情報が登録されログイン画面に遷移
    public function test_user_can_register_and_redirect_to_profile_edit()
    {
        $url = route('register');

        $data = [
            'username' => 'TEST USER',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post($url, $data);
        $response->assertStatus(302);   // ステータスコード302を確認（リダイレクト）
        $response->assertRedirect(route('profile.edit'));

        // データベースにユーザーが作成されたことを確認
        $this->assertDatabaseHas('users', [
            'username' => 'TEST USER',
            'email' => 'test@example.com',
        ]);

        // パスワードがハッシュ化されて保存されていることを確認
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('password123', $user->password));
    }
}
