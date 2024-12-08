<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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
}
