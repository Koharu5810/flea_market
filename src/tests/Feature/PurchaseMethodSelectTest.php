<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Helpers\TestHelper;
use App\Models\Item;
use App\Models\Category;

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

        $categories = Category::take(2)->get();
        $item = Item::factory()->create(['name' => 'Test Item']);
        $item->categories()->attach($categories->pluck('id'));

        return [$user, $item];
    }
    public function test_purchase_page_displays_payment_methods()
    {
        [$user, $item] = $this->PurchasePageShow();

        $response = $this->get(route('purchase.show', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            '<select name="payment_method"',
            '<option value="" disabled selected>選択してください</option>',
            '<option value="コンビニ支払い"',
            '<option value="カード支払い"',
        ], false);
    }
    public function test_select_purchase_method_and_view_is_updated()
    {
        [$user, $item] = $this->PurchasePageShow();

        $selectedPaymentMethod = 'カード支払い';
        $response = $this->post(route('purchase.checkout', ['item_id' => $item->id]), [
            'payment_method' => $selectedPaymentMethod,
        ]);

        $response = $this->get(route('purchase.show', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee('#payment_method');
        $response->assertSee($selectedPaymentMethod); // 小計画面に選択した支払い方法が表示されているか確認
    }
}
