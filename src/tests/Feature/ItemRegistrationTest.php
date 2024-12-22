<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Helpers\TestHelper;
use App\Models\Category;

class ItemRegistrationTest extends TestCase
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
        $this->seed(); // シーダーを実行
    }

    public function test_login_user_can_create_a_product()
    {
        $user = TestHelper::userLogin();

        Storage::fake('public');

        $categories = Category::take(3)->get();
        $categoryIds = $categories->pluck('id')->toArray();

        $response = $this->get(route('show.sell'));
        $response->assertStatus(200);
        $response->assertSee('出品する');

        // テスト画像データ作成
        $imagePath = storage_path('app/public/images/clock.jpg');
        $image = new UploadedFile($imagePath, 'clock.jpg', null, null, true);

        $response = $this->post(route('sell'), [
            'image' => $image,
            'category' => $categoryIds,
            'brand' => 'テストブランド',
            'item_condition' => '1',  // 良好
            'name' => 'テスト商品',
            'description' => 'テスト商品の説明',
            'price' => 1200,
        ]);

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'brand' => 'テストブランド',
            'item_condition' => '1',  // 良好
            'name' => 'テスト商品',
            'description' => 'テスト商品の説明',
            'price' => 1200,
        ]);

        // 画像が保存されているか確認
        $this->assertTrue(
            Storage::disk('public')->exists('items/' . $image->hashName()),
            'The uploaded image does not exist in the storage.'
        );

        // 中間テーブルにカテゴリが同期されているか確認
        foreach ($categoryIds as $categoryId) {
            $this->assertDatabaseHas('item_category', [
                'category_id' => $categoryId,
            ]);
        }

        $response->assertRedirect(route('home'));
        $response->assertSessionDoesntHaveErrors();
    }
}
