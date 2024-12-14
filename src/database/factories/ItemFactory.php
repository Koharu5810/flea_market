<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'address_id' => UserAddress::factory()->create()->id ?? null,
            'uuid' => Str::uuid(),
            'name' => $this->faker->word,
            'image' => $this->faker->imageUrl(640, 480, 'products', true), // ダミー画像URL
            'item_condition' => $this->faker->numberBetween(1, 4),
            'description' => $this->faker->paragraph,
            'brand' => $this->faker->company, // ブランド名 (nullable)
            'price' => $this->faker->randomFloat(0, 100, 10000),
            'is_sold' => $this->faker->boolean,
        ];
    }
}
