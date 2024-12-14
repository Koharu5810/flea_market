<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\Item;

class ItemTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_show_all_items()
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);

        Item::all();
    }
    public function test_purchased_items_are_marked_as_sold()
    {
        $this->seed();
        $items = Item::all();

        $soldItems = $items->random(3);
        foreach ($soldItems as $item) {
            $item->update(['is_sold' => true]);
        }

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        // $response->assertSee('Sold');
    }
}
