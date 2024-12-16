<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Category;
use Tests\Helpers\TestHelper;

class CommentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

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

    public function test_login_user_can_send_comment()
    {
        [$response, $item, $user] = $this->openItemDetailPage();

        $commentData = [
            'comment' => 'テストコメント',
        ];

        $postResponse = $this->post(route('comments.store', ['item_id' => $item->id]), $commentData);

        $postResponse->assertStatus(302);
        $postResponse->assertRedirect(route('item.detail', ['item_id' => $item->id]));

        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'comment' => 'テストコメント',
            'user_id' => $user->id,
        ]);
    }
}
