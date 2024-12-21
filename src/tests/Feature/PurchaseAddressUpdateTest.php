<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Helpers\TestHelper;
use App\Models\Item;
use App\Models\Category;
use App\Models\UserAddress;

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
        $item = Item::factory()->create(['name' => 'Test Item']);
        $item->categories()->attach($categories->pluck('id'));

        $response = $this->get(route('edit.purchase.address', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee('更新する');

        return [$user, $userAddress, $item, $response];
    }
    private function updateAddress($item, $user, $newAddressData)
    {
        $response = $this->patch(route('update.purchase.address', ['item_id' => $item->id]), $newAddressData);
        $response->assertRedirect(route('purchase.show', ['item_id' =>$item->id]));

        $this->assertDatabaseHas('user_addresses', array_merge(['user_id' => $user->id], $newAddressData));

        $response = $this->get(route('purchase.show', ['item_id' => $item->id]));
        $response->assertStatus(200);

        $response->assertSee($newAddressData['postal_code']);
        $response->assertSee($newAddressData['address']);
        $response->assertSee($newAddressData['building']);
    }

// 送付先住所変更画面で登録した住所が商品購入画面に反映されている
    public function test_address_update_with_purchase()
    {
        [$user, $userAddress, $item] = $this->purchaseAddressUpdatePageShow();

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
}
