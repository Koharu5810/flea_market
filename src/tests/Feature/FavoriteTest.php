<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
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

    public function openItemDetailPage()
    {
        $user = TestHelper::userLogin();

        $categories = Category::take(2)->get();
        $item = Item::factory()->create(['name' => 'Test Item']);
        $item->categories()->attach($categories->pluck('id'));

        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertStatus(200);

        return [$response, $item, $user];
    }

// いいねアイコンを押下することでいいねした商品として登録
    public function test_item_can_be_favorited_by_clicking_favorite_icon()
    {
        [$response, $item, $user] = $this->openItemDetailPage();

        $favoriteIcon = asset('storage/app/favorite.png');
        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertSee($favoriteIcon, false);

        $this->post(route('item.favorite', ['item_id' => $item->id]))
            ->assertStatus(302)
            ->assertRedirect(route('item.detail', ['item_id' => $item->id]));

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
// 追加済のアイコンは色が変化する
    public function test_favorite_icon_changes_color_when_item_is_favorited()
    {
        [$response, $item, $user] = $this->openItemDetailPage();

        $favoriteIcon = asset('storage/app/favorite.png');
        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertSee($favoriteIcon, false);

        $this->post(route('item.favorite', ['item_id' => $item->id]))
            ->assertStatus(302)
            ->assertRedirect(route('item.detail', ['item_id' => $item->id]));

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $favoritedIcon = asset('storage/app/favorited.png');
        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertSee($favoritedIcon, false);
    }
// 再度いいねアイコンを押下するといいねを解除できる
    public function test_favorite_icon_toggles_off()
    {
        [$response, $item, $user] = $this->openItemDetailPage();

        $favoriteIcon = asset('storage/app/favorite.png');
        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertSee($favoriteIcon, false);

        $this->post(route('item.favorite', ['item_id' => $item->id]))
            ->assertStatus(302)
            ->assertRedirect(route('item.detail', ['item_id' => $item->id]));

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $favoritedIcon = asset('storage/app/favorited.png');
        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertSee($favoritedIcon, false);

        // 2回目のクリックでお気に入りを解除
        $this->post(route('item.favorite', ['item_id' => $item->id]))
            ->assertStatus(302)
            ->assertRedirect(route('item.detail', ['item_id' => $item->id]));

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $favoriteIcon = asset('storage/app/favorite.png');
        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertSee($favoriteIcon, false);
    }
}
