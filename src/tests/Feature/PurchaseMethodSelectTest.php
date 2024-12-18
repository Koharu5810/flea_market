<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Helpers\TestHelper;
use App\Models\Item;
use App\Models\Category;
use App\Models\UserAddress;

class PurchaseMethodSelectTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_purchase_page_show()
    {
        $user = TestHelper::userLogin();
        UserAddress::factory()->create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
            'building' => 'テストビル',
        ]);

        $categories = Category::take(2)->get();
        $item = Item::factory()->create(['name' => 'Test Item']);
        $item->categories()->attach($categories->pluck('id'));

        $this->get(route('purchase.show', ['item_id' => $item->id]))
            ->assertStatus(200)
            ->assertSee('支払い方法');
    }

}
