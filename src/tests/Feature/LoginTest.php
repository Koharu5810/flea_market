<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson($url, $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'username' => 'ユーザー名またはメールアドレスを入力してください',
        ]);
    }
}
