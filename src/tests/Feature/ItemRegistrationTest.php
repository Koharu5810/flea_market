<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Helpers\TestHelper;

class ItemRegistrationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_example()
    {
        $user = TestHelper::userLogin();

        $response = $this->get(route('show.sell', $user->id));
        $response->assertStatus(200);
    }
}
