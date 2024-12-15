<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Tests\Helpers\TestHelper;


class FavoriteTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_item_can_be_favorited_by_clicking_favorite_icon()
    {
        $user = TestHelper::userLogin();
        $this->seed();
        $item = Item::with(['categories', 'comments.user', 'favoriteBy'])->first();

        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertStatus(200);

        $favoriteIcon = $item->isFavoriteBy(auth()->user())->asset('storage/app/favorited.png');
        $response->assertSee($favoriteIcon);
    }
}
