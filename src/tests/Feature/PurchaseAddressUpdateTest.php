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

    private function PurchaseAddressUpdatePageShow()
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

        return [$user, $userAddress, $item, $response];
    }
    public function test_address_update()
    {
        [$user, $userAddress, $item, $response] = $this->PurchaseAddressUpdatePageShow();

        $newAddressData = [
            'postal_code' => '987-6543',
            'address' => '新テスト住所',
            'building' => '新テストビル',
        ];

        $response->assertSee('更新する');

        $response = $this->patch(route('update.purchase.address', ['item_id' => $item->id]), $newAddressData);
        $response->assertRedirect(route('purchase.show', ['item_id' =>$item->id]));

        $this->assertDatabaseHas('user_addresses', [
            'user_id' => $user->id,
            'postal_code' => '987-6543',
            'address' => '新テスト住所',
            'building' => '新テストビル',
        ]);

        $response = $this->get(route('purchase.show', ['item_id' => $item->id]));
        $response->assertStatus(200);

        $response->assertSee('987-6543');
        $response->assertSee('新テスト住所');
        $response->assertSee('新テストビル');
    }
}
