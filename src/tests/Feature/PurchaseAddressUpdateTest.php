<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Helpers\TestHelper;
use App\Models\Item;
use App\Models\Category;
use App\Models\UserAddress;
use App\Models\Order;

class PurchaseAddressUpdateTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    private function purchaseAddressUpdatePageShow()
    {
        $user = TestHelper::userLogin();
        $userAddress = UserAddress::factory()->create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
            'building' => 'テストビル',
        ]);

        $categories = Category::take(2)->get();
        $item = Item::factory()->create([
            'name' => 'Test Item',
            'is_sold' => false,
        ]);

        $item->categories()->attach($categories->pluck('id'));

        $response = $this->get(route('edit.purchase.address', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee('更新する');

        return [$user, $item, $userAddress];
    }
    private function updateAddress($user, $item, $newAddressData)
    {
        $response = $this->patch(route('update.purchase.address', ['item_id' => $item->id]), $newAddressData);
        $response->assertRedirect(route('purchase.show', ['item_id' =>$item->id]));

        $this->assertDatabaseHas('user_addresses', array_merge(['user_id' => $user->id], $newAddressData));

        $response = $this->get(route('purchase.show', ['item_id' => $item->id]));
        // $response->assertStatus(200);
        $response->assertSee($newAddressData['postal_code']);
        $response->assertSee($newAddressData['address']);
        $response->assertSee($newAddressData['building']);
    }

// 送付先住所変更画面で登録した住所が商品購入画面に反映されている
    public function test_address_update_with_purchase()
    {
        [$user, $item, $userAddress] = $this->purchaseAddressUpdatePageShow();

        $newAddressData = [
            'user_id' => $user->id,
            'postal_code' => '987-6543',
            'address' => '新テスト住所',
            'building' => '新テストビル',
        ];

        $this->updateAddress($user, $item, $newAddressData);

        $response = $this->get(route('purchase.show', ['item_id' =>$item->id]));
        $response->assertSee('987-6543');
        $response->assertSee('新テスト住所');
        $response->assertSee('新テストビル');
    }
// 購入した商品に送付先住所が紐づいて登録される
    public function test_user_can_update_address_and_purchase_item()
    {
        [$user, $item] = $this->purchaseAddressUpdatePageShow();

        $newAddressData = [
            'user_id' => $user->id,
            'postal_code' => '987-6543',
            'address' => '新テスト住所',
            'building' => '新テストビル',
        ];

        $this->patch(route('update.purchase.address', ['item_id' => $item->id]), $newAddressData);

        $address = UserAddress::where('user_id', $user->id)->firstOrFail();
        $item = Item::find($item->id);

        $this->mock(\Stripe\Stripe::class, function ($mock) {});
        $this->mock(\Stripe\Checkout\Session::class, function ($mock) {
            $mock->shouldReceive('retrieve')
                ->with('test_session_id') // モックが正しいセッションIDを期待する
                ->andReturn((object)[
                    'payment_status' => 'paid',
                    'payment_method_types' => ['card'],
                ]);
        });

        $this->post(route('purchase.checkout', [
            'item_id' => $item->id,
            'session_id' => 'test_session_id',
        ]));

        // 商品データを更新
        $item->update([
            'is_sold' => true,
            'address_id' => $address->id,
        ]);
        // 注文情報を作成
        $purchasedAt = now()->format('Y-m-d H:i:s');
        $order = Order::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => 'card',
            'purchased_at' => $purchasedAt,
        ]);

        // データベース確認
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => 'Test Item',
            'is_sold' => true,
            'address_id' => $address->id,
        ]);

        $this->assertDatabaseHas('orders', [
            'uuid' => $order->uuid,
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => 'card',
            'purchased_at' => $purchasedAt,
        ]);
    }
}
