<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Category;
use Psy\CodeCleaner\ReturnTypePass;
use Tests\Helpers\TestHelper;

class CommentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    private function createItemWithCategories()
    {
        $categories = Category::take(2)->get();
        $item = Item::factory()->create(['name' => 'Test Item']);
        $item->categories()->attach($categories->pluck('id'));

        return $item;
    }
    private function openItemDetailPage()
    {
        $user = TestHelper::userLogin();

        $item = $this->createItemWithCategories();

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

        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertSee('コメントを送信する');

        $postResponse = $this->post(route('comments.store', ['item_id' => $item->id]), $commentData);

        $postResponse->assertStatus(302);
        $postResponse->assertRedirect(route('item.detail', ['item_id' => $item->id]));

        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'comment' => 'テストコメント',
            'user_id' => $user->id,
        ]);
    }
    public function test_guest_user_cant_send_comment()
    {
        $categories = Category::take(2)->get();
        $item = Item::factory()->create(['name' => 'Test Item']);
        $item->categories()->attach($categories->pluck('id'));

        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertStatus(200);

        $commentData = [
            'comment' => 'テストコメント',
        ];
        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertSee('コメントを送信する');

        $postResponse = $this->post(route('comments.store', ['item_id' => $item->id]), $commentData);

        $postResponse->assertRedirect(route('login'));

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'comment' => 'テストコメント',
        ]);
    }
    public function test_comment_validation_error_when_comment_is_missing()
    {
        [$response, $item] = $this->openItemDetailPage();

        $commentData = ['comment' => '',];

        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertSee('コメントを送信する');

        $postResponse = $this->post(route('comments.store', ['item_id' => $item->id]), $commentData);
        $postResponse->assertStatus(302);
        $postResponse->assertSessionHasErrors([
            'comment' => 'コメントを入力してください',
        ]);
    }
    public function test_comment_validation_error_when_comment_exceeds_255_characters()
    {
        [$response, $item] = $this->openItemDetailPage();

        $commentData = [
            'comment' => str_repeat('a', 256),
        ];

        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertSee('コメントを送信する');

        $postResponse = $this->post(route('comments.store', ['item_id' => $item->id]), $commentData);
        $postResponse->assertStatus(302);
        $postResponse->assertSessionHasErrors([
            'comment' => 'コメントは255文字以内で入力してください',
        ]);
    }
}
