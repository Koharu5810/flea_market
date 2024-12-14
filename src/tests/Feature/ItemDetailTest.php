<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

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
        $this->seed();
        $item = Item::with(['categories', 'comments.user', 'favoriteBy'])->first();

        // データが取得できない場合はエラーハンドリング
        if (!$item) {
            $this->fail('No items found in the database. Ensure data is seeded or created.');
        }

        $imagePath = asset('storage/' . $item->image);
    // dump($imagePath);

        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
    // dump($response->getContent());
        $response->assertStatus(200);

        // $response->assertSee($imagePath, false);
        $this->assertStringContainsString($imagePath, $response->getContent());

        return [$response, $item];
    }

    public function test_item_detail_page_displays_correct_information()
    {
        [$response, $item] = $this->openItemDetailPage();

        // 商品に「いいね」を付与
        $item->favoriteBy()->attach(User::factory()->count(5)->create()->pluck('id'));

        // 商品情報が正しく表示されていることを確認
        $response->assertSeeText($item->name);
        $response->assertSee($item->image);
        $response->assertSeeText($item->brand);
        $response->assertSeeText(number_format($item->price));
        $response->assertSeeText($item->item_condition);
        $response->assertSeeText($item->description);

        // カテゴリ情報が正しく表示されていることを確認
        foreach ($item->categories as $category) {
            $response->assertSeeText($category->name);
        }

        // コメント情報が正しく表示されていることを確認
        foreach ($item->comments as $comment) {
            $response->assertSeeText($comment->user->username); // コメントしたユーザー名
            $response->assertSeeText($comment->content);    // コメント内容
        }

        $response->assertSeeText($item->favoriteBy->count());
        $response->assertSeeText($item->comments->count());

        // 「いいね」アイコンのパスが正しいことを確認
        $favoriteIcon = $item->isFavoriteBy(auth()->user())
            ? asset('storage/app/favorited.png')
            : asset('storage/app/favorite.png');
        $response->assertSee($favoriteIcon);
        // コメントアイコンのパスが正しいことを確認
        $response->assertSee(asset('storage/app/comment.png'));
    }


}
