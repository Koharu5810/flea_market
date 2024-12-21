<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Category;
use Tests\Helpers\TestHelper;

class ProfileTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_user_can_view_profile_page_with_required_information()
    {
        $user = TestHelper::userLogin();

        $categories = Category::take(2)->get();
        $item = Item::factory()->create(['name' => 'Test Item']);
        $item->categories()->attach($categories->pluck('id'));

        // 購入商品を作成
        $purchasedProducts = Item::factory()->count(2)->create([
            'name' => 'Purchased Product',
        ]);

        // 出品商品を作成
        $userProducts = Item::factory()->count(3)->create([
            'user_id' => $user->id,
            'name' => 'Test Product',
        ]);

        $response = $this->get(route('profile.mypage', $user->id));
        $response->assertStatus(200);
        $response->assertSee(asset('storage/' . $user->profile_image));
        $response->assertSee($user->username);

        $response = $this->get(route('profile.mypage', ['tab' => 'unknown']));
        $response->assertStatus(200);
        $response->assertDontSee('Test Item');


        foreach ($userProducts as $product) {
            $response->assertSee(json_decode($product->name));
        }

        // 購入した商品が表示されていることを確認
        $response = $this->get(route('profile.mypage', ['tab' => 'buy']));
        foreach ($purchasedProducts as $product) {
            $response->assertSee(json_decode($product->name));
        }
    }
}
