<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Category;
use Tests\Helpers\TestHelper;


class FavoriteTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

// 共通処理
    private function openItemDetailPage()
    {
        $user = TestHelper::userLogin();

        $categories = Category::take(2)->get();
        $item = Item::factory()->create(['name' => 'Test Item']);
        $item->categories()->attach($categories->pluck('id'));

        $this->get(route('item.detail', ['item_id' => $item->id]))
            ->assertStatus(200);

        return [$user, $item];
    }
    private function toggleFavorite($item)
    {
        $this->post(route('item.favorite', ['item_id' => $item->id]))
            ->assertStatus(302)
            ->assertRedirect(route('item.detail', ['item_id' => $item->id]));
    }
    private function assertFavoriteState($user, $item, $isFavorited)
    {
        $icon = $isFavorited ? 'favorited.png' : 'favorite.png';
        $this->get(route('item.detail', ['item_id' => $item->id]))
            ->assertSee(asset("images/app/{$icon}"), false);

        $method = $isFavorited ? 'assertDatabaseHas' : 'assertDatabaseMissing';
        $this->{$method}('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

// いいねアイコンを押下することでいいねした商品として登録
    public function test_item_can_be_favorited_by_clicking_favorite_icon()
    {
        [$user, $item] = $this->openItemDetailPage();

        $this->assertFavoriteState($user, $item, false); // 初期状態: いいねしていない
        $this->toggleFavorite($item);                   // いいね処理
        $this->assertFavoriteState($user, $item, true); // 状態確認: いいね済み
    }
// 追加済のアイコンは色が変化する
    public function test_favorite_icon_changes_color_when_item_is_favorited()
    {
        [$user, $item] = $this->openItemDetailPage();

        $this->assertFavoriteState($user, $item, false);
        $this->toggleFavorite($item);
        $this->assertFavoriteState($user, $item, true);
    }
// 再度いいねアイコンを押下するといいねを解除できる
    public function test_favorite_icon_toggles_off()
    {
        [$user, $item] = $this->openItemDetailPage();

        $this->assertFavoriteState($user, $item, false);
        $this->toggleFavorite($item);
        $this->assertFavoriteState($user, $item, true);

        // 2回目のクリックでお気に入りを解除
        $this->toggleFavorite($item);
        $this->assertFavoriteState($user, $item, false);
    }
}
