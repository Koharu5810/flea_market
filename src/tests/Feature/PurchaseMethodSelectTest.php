<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
    use RefreshDatabase;

    private function PurchasePageShow()
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

        return [$user, $item];
    }
    private function accessPurchasePage($itemId)
    {
        $response = $this->get(route('purchase.show', ['item_id' => $itemId]));
        $response->assertStatus(200);
        $response->assertSee('支払い方法');
        $response->assertSee('123-4567');
        $response->assertSee('テスト住所');
        $response->assertSee('テストビル');

        return $response;
    }

    public function test_payment_method_is_required()
    {
        [$user, $item] = $this->PurchasePageShow();
        $this->accessPurchasePage($item->id);

        $response = $this->actingAs($user)
            ->post(route('purchase.checkout', ['item_id' => $item->id]), [
                '_token' => csrf_token(),
                'payment_method' => '', // 支払い方法未選択
            ]);

        $response->assertSessionHasErrors([
            'payment_method' => '支払い方法を選択してください',
        ]);
    }

    public function test_valid_payment_method_can_be_submitted()
    {
        [$user, $item] = $this->PurchasePageShow();
        $this->accessPurchasePage($item->id);

        $response = $this->actingAs($user)
            ->post(route('purchase.checkout', ['item_id' => $item->id]), [
                '_token' => csrf_token(),
                'payment_method' => 'カード支払い',
                'address' => 'テスト住所',
            ]);

        $response->assertSessionHasNoErrors(); // エラーがないことを確認
        $response->assertRedirect(); // 成功時のリダイレクトを確認
    }
}
