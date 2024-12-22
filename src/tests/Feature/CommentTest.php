<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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

    private function createItemWithCategories()
    {
        $categories = Category::take(2)->get();

        $item = Item::factory()->create(['name' => 'Test Item']);
        $item->categories()->attach($categories->pluck('id'));

        return $item;
    }
    private function loginAndGetItemDetailPage()
    {
        $user = TestHelper::userLogin();
        $item = $this->createItemWithCategories();

        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee('コメントを送信する');

        return [$user, $item, $response];
    }
        private function GetItemDetailPageAsGuest()
    {
        $item = $this->createItemWithCategories();

        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee('コメントを送信する');

        return [$item, $response];
    }

// ログインユーザはコメントを送信できる
    public function test_login_user_can_send_comment()
    {
        [$user, $item, $response] = $this->loginAndGetItemDetailPage();

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
    public function test_guest_user_cant_send_comment()
    {
        [$item, $response] = $this->GetItemDetailPageAsGuest();

        $commentData = [
            'comment' => 'テストコメント',
        ];

        $postResponse = $this->post(route('comments.store', ['item_id' => $item->id]), $commentData);

        $postResponse->assertRedirect(route('login'));

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'comment' => 'テストコメント',
        ]);
    }
    public function test_comment_validation_error_when_comment_is_missing()
    {
        [$item, $response] = $this->loginAndGetItemDetailPage();

        $commentData = ['comment' => '',];

        $postResponse = $this->post(route('comments.store', ['item_id' => $item->id]), $commentData);
        $postResponse->assertStatus(302);
        $postResponse->assertSessionHasErrors([
            'comment' => 'コメントを入力してください',
        ]);
    }
    public function test_comment_validation_error_when_comment_exceeds_255_characters()
    {
        [$item, $response] = $this->loginAndGetItemDetailPage();

        $commentData = [
            'comment' => str_repeat('a', 256),
        ];

        $postResponse = $this->post(route('comments.store', ['item_id' => $item->id]), $commentData);
        $postResponse->assertStatus(302);
        $postResponse->assertSessionHasErrors([
            'comment' => 'コメントは255文字以内で入力してください',
        ]);
    }
}
