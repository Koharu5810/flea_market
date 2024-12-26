<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class SearchTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_search_form_is_displayed()
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);

        // 検索フォームが表示されていることを確認
        $response->assertSee('<form action="', false);
        $response->assertSee('name="query"', false);
        $response->assertSee('placeholder="なにをお探しですか？"', false);
    }
    public function test_items_can_be_searched_by_name()
    {
        // ダミー商品を作成
        $matchingItem = Item::factory()->create(['name' => 'Sample Product']);
        $nonMatchingItem = Item::factory()->create(['name' => 'Unrelated Item']);

        $response = $this->get(route('search', ['query' => 'Sample']));
        $response->assertStatus(200);

        // 部分一致する商品が表示されていることを確認
        $response->assertSeeText($matchingItem->name);
        // 部分一致しない商品が表示されていないことを確認
        $response->assertDontSeeText($nonMatchingItem->name);
    }
    public function test_search_query_is_retained_across_tabs()
    {
        // ダミー商品を作成
        $matchingItem = Item::factory()->create(['name' => 'Sample Product']);
        $nonMatchingItem = Item::factory()->create(['name' => 'Unrelated Item']);

        /** @var \App\Models\User $user */   // $userの型解析ツールエラーが出るため追記
        $user = User::factory()->create();
        $this->actingAs($user);
        // 商品をお気に入りに登録
        $user->favorites()->attach($matchingItem->id);

        $response = $this->get(route('home', ['query' => 'Sample']));
        $response->assertStatus(200);

        // 検索結果が表示されていることを確認
        $response->assertSeeText($matchingItem->name);
        $response->assertDontSeeText($nonMatchingItem->name);

        $response = $this->get(route('home', ['tab' => 'mylist', 'query' => 'Sample']));
        $response->assertStatus(200);

        // 検索状態が保持され、同じ結果が表示されることを確認
        $response->assertSeeText($matchingItem->name);
        $response->assertDontSeeText($nonMatchingItem->name);
    }
}
