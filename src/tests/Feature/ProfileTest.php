<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $item = Item::factory()->create([
            'name' => 'Test Item'
        ]);
        $item->categories()->attach($categories->pluck('id'));

        // 購入商品を作成
        $purchasedProduct = Item::factory()->create([
            'name' => '購入した商品',
        ]);

        // 出品商品を作成
        $userProduct = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '出品した商品',
        ]);

        $response = $this->get(route('profile.mypage', $user->id));
        $response->assertStatus(200);

        if ($user->profile_image) {
            $response->assertSee(asset('storage/' . $user->profile_image));
        } else {
            $response->assertDontSee('<img id="previewImage"');
        }
        $response->assertSee($user->username);

        // 出品した商品が表示されていることを確認
        $response = $this->get(route('profile.mypage', ['tab' => 'sell']));
        $response->assertSee(json_decode($userProduct->name));

        // 購入した商品が表示されていることを確認
        $response = $this->get(route('profile.mypage', ['tab' => 'buy']));
        $response->assertSee(json_decode($purchasedProduct->name));
    }
}
