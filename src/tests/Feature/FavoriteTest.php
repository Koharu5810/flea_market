<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
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

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush(); // テスト開始前にキャッシュをクリア
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        gc_collect_cycles(); // ガベージコレクションを手動で実行
    }

// 共通処理
    private function openItemDetailPage()
    {
        $user = TestHelper::userLogin();

        $categories = Category::take(2)->get();
        $item = Item::factory()->create(['name' => 'Test Item']);
        $item->categories()->attach($categories->pluck('id'));

        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertStatus(200);

        return [$response, $item, $user];
    }
    private function toggleFavorite($item)
    {
        return $this->post(route('item.favorite', ['item_id' => $item->id]))
            ->assertStatus(302)
            ->assertRedirect(route('item.detail', ['item_id' => $item->id]));
    }
    private function assertFavoriteIcon($item, $favoriteIcon)
    {
        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertSee($favoriteIcon, false);
    }

// いいねアイコンを押下することでいいねした商品として登録
    public function test_item_can_be_favorited_by_clicking_favorite_icon()
    {
        [$response, $item, $user] = $this->openItemDetailPage();

        $this->assertFavoriteIcon($item, asset('storage/app/favorite.png'));

        $this->toggleFavorite($item);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
// 追加済のアイコンは色が変化する
    public function test_favorite_icon_changes_color_when_item_is_favorited()
    {
        [$response, $item, $user] = $this->openItemDetailPage();

        $this->assertFavoriteIcon($item, asset('storage/app/favorite.png'));

        $this->toggleFavorite($item);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->assertFavoriteIcon($item, asset('storage/app/favorited.png'));

    }
// 再度いいねアイコンを押下するといいねを解除できる
    public function test_favorite_icon_toggles_off()
    {
        [$response, $item, $user] = $this->openItemDetailPage();

        $this->assertFavoriteIcon($item, asset('storage/app/favorite.png'));

        $this->toggleFavorite($item);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->assertFavoriteIcon($item, asset('storage/app/favorited.png'));

        // 2回目のクリックでお気に入りを解除
        $this->toggleFavorite($item);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->assertFavoriteIcon($item, asset('storage/app/favorite.png'));
    }
}
