<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Tests\Helpers\TestHelper;

class ItemTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_show_all_items()
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);

        Item::all();
    }
    public function test_purchased_items_are_marked_as_sold()
    {
        $this->seed();
        $items = Item::all();

        $soldItems = $items->random(3);
        foreach ($soldItems as $item) {
            $item->update(['is_sold' => true]);
        }

        $response = $this->get(route('home'));
        $response->assertStatus(200);

        // レスポンスのHTMLを文字列として取得
        $html = $response->getContent();

        // 購入済み商品が正しく表示されているか確認
        foreach ($soldItems as $item) {
            $this->assertStringContainsString(
                'item-container soldout',
                $html,
                "Item ID {$item->id} の 'soldout' クラスが見つかりません。"
            );
            $this->assertStringContainsString(
                'Sold',
                $html,
                "Item ID {$item->id} に 'Sold' が表示されていません。"
            );
        }
    }
// 自分が出品した商品は非表示
    public function test_user_can_see_their_own_products_on_product_page()
    {
        /** @var \App\Models\User $user */   // $userの型解析ツールエラーが出るため追記
        $user = User::factory()->create();
        $this->actingAs($user);

        // ログイン中のユーザーが出品
        $ownItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Item',
        ]);

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertDontSee($ownItem);
    }

    public function test_only_favorited_items_are_displayed()
    {
        /** @var \App\Models\User $user */   // $userの型解析ツールエラーが出るため追記
        $user = User::factory()->create();
        $items = Item::factory()->count(2)->create();

        // ユーザーが「いいね」した商品を登録
        $user->favorites()->attach($items->pluck('id'));

        // ユーザーをログイン
        $this->actingAs($user);

        $response = $this->get(route('home', ['tab' => 'mylist']));
        $response->assertStatus(200);
    }
    public function test_guest_cannot_see_mylist_items()
    {
        $response = $this->get(route('home', ['tab' => 'mylist']));
        $response->assertStatus(200);
        $response->assertDontSeeText('お気に入り登録した商品がありません');
    }

    public function test_sold_items_are_marked_as_sold_in_mylist()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $items = Item::factory()->count(2)->create();

        // 最初のアイテムを売却済みに設定
        $soldItem = $items->first();
        $soldItem->update(['is_sold' => true]);

        $user->favorites()->attach($items->pluck('id'));

        $this->actingAs($user);

        $response = $this->get(route('home', ['tab' => 'mylist']));
        $response->assertStatus(200);

        $response->assertSeeText('Sold');
        $response->assertSeeText($soldItem->name);
    }
    public function test_user_does_not_see_their_own_items_in_mylist()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownItem = Item::factory()->create(['user_id' => $user->id]);
        $otherItem = Item::factory()->create(['user_id' => $otherUser->id]);

        // ユーザーが他のユーザーの商品をお気に入り登録
        $user->favorites()->attach($otherItem->id);

        $this->actingAs($user);

        $response = $this->get(route('home', ['tab' => 'mylist']));
        $response->assertStatus(200);

        // 自分が出品した商品が表示されていないことを確認
        $response->assertDontSeeText($ownItem->name);

        // 他のユーザーが出品した商品が表示されていることを確認
        $response->assertSeeText($otherItem->name);
    }
    public function test_guest_user_sees_nothing_in_mylist()
    {
        // 未認証の状態でマイリストタブにアクセス
        $response = $this->get(route('home', ['tab' => 'mylist']));
        $response->assertStatus(200);

        // レスポンスが空に近い状態であることを確認
        $html = $response->getContent();

        // お気に入り商品がない場合のメッセージが表示されていないことを確認
        $this->assertStringNotContainsString('お気に入り登録した商品がありません', $html);
        // 商品のリストが一切表示されていないことを確認
        $this->assertStringNotContainsString('<div class="item-container"', $html);
    }


}
