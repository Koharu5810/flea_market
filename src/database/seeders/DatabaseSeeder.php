<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            CategoriesTableSeeder::class,
            ItemsTableSeeder::class,
            FavoritesTableSeeder::class,
            CommentsTableSeeder::class,
            ChatRoomsTableSeeder::class,
            MessagesTableSeeder::class,
        ]);
    }
}
