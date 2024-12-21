<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Helpers\TestHelper;

class ProfileUpdateTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_user_can_update_profile()
    {
        $user = TestHelper::userLogin();

        $user->update([
            'profile_image' => 'images/profile/test_image.png',
            'username' => 'TestUser',
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
            'building' => 'テストビル',
        ]);

        $response = $this->get(route('profile.edit', $user->id));
        $response->assertStatus(200);

        $response->assertSee(asset('storage/' . $user->profile_image));
        $response->assertSee($user->username);
        $response->assertSee($user->postal_code);
        $response->assertSee($user->address);
        $response->assertSee($user->building);
    }
}
