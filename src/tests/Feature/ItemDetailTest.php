<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;

class ItemDetailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function openItemDetailPage()
    {
        $categories = Category::take(2)->get();

        // ダミー商品を作成
        $item = Item::factory()
            ->create([
                'name' => 'Test Product',
                'brand' => 'Test Brand',
                'price' => 1000,
                'description' => 'This is a test description.',
                'item_condition' => 1,
            ]);

        $item->categories()->attach($categories->pluck('id'));

        Comment::factory(3)->create([
            'item_id' => $item->id,
            'user_id' => User::factory()->create()->id,
        ]);

        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertStatus(200);

        return [$response, $item];
    }

    public function test_item_detail_page_displays_correct_information()
    {
        [$response, $item] = $this->openItemDetailPage();

        // 商品に「いいね」を付与
        $item->favoriteBy()->attach(User::factory()->count(5)->create()->pluck('id'));

        // 商品情報が正しく表示されていることを確認
        $response->assertSeeText($item->name);
        $response->assertSeeText($item->brand);
        $response->assertSeeText(number_format($item->price));
        $response->assertSeeText($item->item_condition);
        $response->assertSeeText($item->description);

        // カテゴリ情報が正しく表示されていることを確認
        foreach ($item->categories as $category) {
            $response->assertSeeText($category->name);
        }

        // コメント情報が正しく表示されていることを確認
        $comments = Comment::where('item_id', $item->id)->get();
        foreach ($comments as $comment) {
            $response->assertSeeText($comment->user->username); // コメントしたユーザー名
            $response->assertSeeText($comment->content);    // コメント内容
        }

        $response->assertSeeText($item->favoriteBy->count());
        $response->assertSeeText($item->comments->count());

        // 「いいね」アイコンのパスが正しいことを確認
        if ($item->isFavoriteBy(auth()->user())) {
            $response->assertSee(asset('storage/app/favorited.png'));
        } else {
            $response->assertSee(asset('storage/app/favorite.png'));
        }
        // コメントアイコンのパスが正しいことを確認
        $response->assertSee(asset('storage/app/comment.png'));
    }


}
