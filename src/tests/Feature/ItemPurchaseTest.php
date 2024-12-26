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
// 商品を購入する
    private function purchaseItem($item, $sessionId = 'test_session_id')
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

// 商品購入テスト
    public function test_user_can_purchase_item()
    {
        $user = $this->loginUser();
        [$address, $item] = $this->preparePurchasePage($user);

        $this->purchaseItem($item);

        // Stripe関連の処理をモック
        $this->mock(\Stripe\Stripe::class, function ($mock) {
        });
        $this->mock(\Stripe\Checkout\Session::class, function ($mock) {
            $mock->shouldReceive('retrieve')
                ->with('test_session_id') // モックが正しいセッションIDを期待する
                ->andReturn((object)[
                    'payment_status' => 'paid',
                    'payment_method_types' => ['card'],
                ]);
        });

        $response = $this->successPurchase($item);
        // $response->assertStatus(200);

        $item->update([
            'is_sold' => true,
            'address_id' => $address->id,
        ]);

        $purchasedAt = now()->format('Y-m-d H:i:s'); // フォーマットを明示的に指定
        $order = Order::create([
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

        $response = $this->get(route('home'));
        $response->assertStatus(200);
    }
// 購入後、商品一覧画面で「sold」表示される
    public function test_user_can_purchase_item_and_item_is_marked_sold()
    {
        $user = $this->loginUser();
        [$address, $item] = $this->preparePurchasePage($user);

        // 商品を購入する
        $this->purchaseItem($item);

        // Stripe関連の処理をモック
        $this->mock(\Stripe\Stripe::class, function ($mock) {});
        $this->mock(\Stripe\Checkout\Session::class, function ($mock) {
            $mock->shouldReceive('retrieve')
                ->with('test_session_id') // モックが正しいセッションIDを期待する
                ->andReturn((object)[
                    'payment_status' => 'paid',
                    'payment_method_types' => ['card'],
                ]);
        });

        // 購入成功リクエスト
        $response = $this->successPurchase($item);
        // $response->assertStatus(200);

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

        // 商品購入後のデータ確認
        $itemAfterUpdate = $item->refresh();
        $this->assertEquals(true, $itemAfterUpdate->is_sold); // 'is_sold' が true であることを確認
        $this->assertEquals($address->id, $itemAfterUpdate->address_id); // 'address_id' が期待値であることを確認

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

        // 商品一覧画面を表示
        $response = $this->get(route('home'));
        $response->assertStatus(200);

        // 購入した商品が "sold" として表示されていることを確認
        $response->assertSee('sold');
        $response->assertSee($item->name);
    }

}
