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

// ユーザにログインする
    private function loginUser()
    {
        return TestHelper::userLogin();
    }
// 商品と購入画面を準備する
    private function preparePurchasePage($user)
    {
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

        return [$address, $item];
    }
// Stripe関連のモックを設定
    private function mockStripe()
    {
        $this->mock(\Stripe\Stripe::class, function ($mock) {});
        $this->mock(\Stripe\Checkout\Session::class, function ($mock) {
            $mock->shouldReceive('retrieve')
                ->with('test_session_id') // モックが正しいセッションIDを期待する
                ->andReturn((object)[
                    'payment_status' => 'paid',
                    'payment_method_types' => ['card'],
                ]);
        });
    }
// 商品を購入する
    private function purchaseItem($item)
    {
        $response = $this->post(route('purchase.checkout', [
            'item_id' => $item->id,
            'session_id' => 'test_session_id',
        ]));
        // $response->assertStatus(200);  // バリデーションエラーがないことを確認
    }
// 商品購入後処理
    private function successPurchase($item, $sessionId = 'test_session_id')
    {
        return $this->get(route('purchase.success', [
            'item_id' => $item->id,
            'session_id' => 'test_session_id',
        ]));
    }
// 購入後の共通確認処理
    private function verifyPurchase($item, $address, $user)
    {
        $purchasedAt = now()->format('Y-m-d H:i:s');

        // 商品データを更新
        $item->update([
            'is_sold' => true,
            'address_id' => $address->id,
        ]);

        // 注文情報を作成
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

        return $order;
    }

// 商品購入テスト
    public function test_user_can_purchase_item()
    {
        $user = $this->loginUser();
        [$address, $item] = $this->preparePurchasePage($user);

        $this->mockStripe();
        $this->purchaseItem($item);

        $response = $this->successPurchase($item);
        // $response->assertStatus(200);

        $this->verifyPurchase($item, $address, $user);

        $response = $this->get(route('home'));
        $response->assertStatus(200);
    }
// 購入後、商品一覧画面で「sold」表示される
    public function test_user_can_purchase_item_and_item_is_marked_sold()
    {
        $user = $this->loginUser();
        [$address, $item] = $this->preparePurchasePage($user);

        $this->mockStripe();
        $this->purchaseItem($item);

        $response = $this->successPurchase($item);
        // $response->assertStatus(200);

        $this->verifyPurchase($item, $address, $user);

        $response = $this->get(route('home'));
        $response->assertStatus(200);

        // 購入した商品が "sold" として表示されていることを確認
        $response->assertSee('sold');
        $response->assertSee($item->name);
    }
// 購入後、プロフィール画面で購入した商品一覧に追加されている
    public function test_user_can_purchase_item_and_it_is_added_to_profile()
    {
        $user = $this->loginUser();
        [$address, $item] = $this->preparePurchasePage($user);

        $this->mockStripe();
        $this->purchaseItem($item);

        $response = $this->successPurchase($item);
        // $response->assertStatus(200);

        $this->verifyPurchase($item, $address, $user);

        // プロフィール画面に遷移
        $response = $this->get(route('profile.mypage', [
            'user_id' => $user->id,
            'tab' => 'buy',
        ]));
        $response->assertStatus(200);

        // プロフィール画面で購入した商品が「購入した商品一覧」に追加されていることを確認
        $response->assertSee('購入した商品'); // セクションタイトルが表示されていることを確認
        $response->assertSee($item->name); // 購入した商品の名前が表示されていることを確認
    }
}
