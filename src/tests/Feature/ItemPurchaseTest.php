<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Tests\Helpers\TestHelper;
use App\Models\Item;
use App\Models\Category;
use App\Models\Order;

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
        $address = $user->user_address()->create([
            'postal_code' => '123-4567',
            'address' => '東京都新宿区テスト',
            'building' => 'テストビル',
        ]);

        $categories = Category::take(2)->get();
        $item = Item::factory()->create([
            'name' => 'Test Item',
            'price' => 1200,
            'is_sold' => false,
            'address_id' => $address->id,
        ]);
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
        $this->mock(\Stripe\Checkout\Session::class, function ($mock) {
            $mock->shouldReceive('retrieve')
                ->andReturn((object)[
                    'payment_status' => 'paid',
                    'payment_method_types' => ['card'],
                ]);
        });

        // $uuid = (string) Str::uuid();
        // $uuid = '56aa6dee-ba58-487f-97bd-bf1756d84e2b';
        $purchasedAt = now()->format('Y-m-d H:i:s'); // フォーマットを明示的に指定

        $response = $this->post(route('purchase.success', [
            'item_id' => $item->id,
            'session_id' => 'test_session_id',
        ]));

        $item->update([
            'is_sold' => true,
            'address_id' => $address->id,
        ]);

        $order = Order::create([
            // 'uuid' => $uuid,
            // 'user_id' => auth()->id(),
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => 'card',
            'purchased_at' => $purchasedAt,
        ]);

        $itemAfterUpdate = $item->refresh();

        // データベースの状態確認
        $this->assertEquals(true, $itemAfterUpdate->is_sold); // 'is_sold'がtrueになっているか確認
        $this->assertEquals($address->id, $itemAfterUpdate->address_id); // 'address_id'が期待値か確認

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'is_sold' => true,
            'address_id' => $address->id,
        ]);

        // // テーブルの確認
        $this->assertDatabaseHas('orders', [
            'uuid' => $order->uuid,
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => 'card',
            'purchased_at' => $purchasedAt,
        ]);

        // $response->assertRedirect(route('purchase.success', ['item_id' => $item->id]));

        // $response->assertStatus(302);
        // $response->assertRedirect(route('home'));
    }
}
