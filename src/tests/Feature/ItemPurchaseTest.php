<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Helpers\TestHelper;
use App\Models\Item;
use App\Models\Category;
use App\Models\UserAddress;

class ItemPurchaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    private function purchasePageShow()
    {
        $user = TestHelper::userLogin();
        $user->user_address()->create([
            'postal_code' => '123-4567',
            'address' => '東京都新宿区テスト',
            'building' => 'テストビル',
        ]);

        $address = UserAddress::factory()->create(['user_id' => $user->id]);

        $categories = Category::take(2)->get();
        $item = Item::factory()->create(['name' => 'Test Item']);
        $item->categories()->attach($categories->pluck('id'));

        $response = $this->get(route('purchase.show', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee('購入する');

        return [$user, $address, $item, $response];
    }
    public function test_user_can_purchase_item()
    {
        [$user, $address, $item, $response] = $this->purchasePageShow();

        // Stripe関連の処理をモック
        $this->mock(\Stripe\Stripe::class, function ($mock) {
        });
        $this->mock(\Stripe\Checkout\Session::class, function ($mock) use ($item) {
            $mock->shouldReceive('retrieve')
                ->andReturn((object)[
                    'payment_status' => 'paid',
                    'payment_method_types' => ['card'],
                ]);
        });

        $data = [
            'uuid' => (string) Str::uuid(),
            'user_id' => auth()->id(),
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => $session->payment_method_types[0] ?? 'unknown',
            'purchased_at' => now(),
        ];

        $response = $this->post(route('purchase.checkout', ['item_id' => $item->id]), $data);
        // $response->assertRedirect(route('purchase.success', ['item_id' => $item->id]));

        $response->assertStatus(302);

        // テーブルの確認
        // $this->assertDatabaseHas('orders', [
        //     'user_id' => $user->id,
        //     'item_id' => $item->id,
        //     'address_id' => $address->id,
        //     'payment_method' => 'card',
        // ]);

        // $this->assertDatabaseHas('items', [
        //     'id' => $item->id,
        //     'is_sold' => true,
        // ]);

        // $response->assertRedirect(route('home'));
    }
}
