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
    private function getItemDetailPage($isLoggedIn = false)
    {
        $user = null;
        if ($isLoggedIn) {
            $user = TestHelper::userLogin();
        }

        $item = $this->createItemWithCategories();

        $this->get(route('item.detail', ['item_id' => $item->id]))
            ->assertStatus(200)
            ->assertSee('コメントを送信する');

        return [$user, $item];
    }
    private function sendComment($itemId, $commentData)
    {
        return $this->post(route('comments.store', ['item_id' => $itemId]), $commentData);
    }

// ログインユーザはコメントを送信できる
    public function test_login_user_can_send_comment()
    {
        [$user, $item] = $this->getItemDetailPage(true);

        $commentData = ['comment' => 'テストコメント'];

        $this->sendComment($item->id, $commentData)
            ->assertStatus(302)
            ->assertRedirect(route('item.detail', ['item_id' => $item->id]));

        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'comment' => 'テストコメント',
            'user_id' => $user->id,
        ]);
    }
// 未認証ユーザはコメントを送信できない
    public function test_guest_user_cant_send_comment()
    {
        [$user, $item] = $this->getItemDetailPage(false);

        $commentData = ['comment' => 'テストコメント'];

        $this->sendComment($item->id, $commentData)
            ->assertRedirect(route('login'));

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'comment' => 'テストコメント',
        ]);
    }
// コメントを空送信するとバリデーションエラー
    public function test_comment_validation_error_when_comment_is_missing()
    {
        [$item] = $this->getItemDetailPage(true);

        $commentData = ['comment' => '',];

        $this->sendComment($item->id, $commentData)
            ->assertStatus(302)
            ->assertSessionHasErrors([
                'comment' => 'コメントを入力してください',
            ]);
    }
// コメントを256文字以上で送信
    public function test_comment_validation_error_when_comment_exceeds_255_characters()
    {
        [$item] = $this->getItemDetailPage(true);

        $commentData = ['comment' => str_repeat('a', 256)];

        $this->sendComment($item->id, $commentData)
            ->assertStatus(302)
            ->assertSessionHasErrors([
                'comment' => 'コメントは255文字以内で入力してください',
            ]);
    }
}
