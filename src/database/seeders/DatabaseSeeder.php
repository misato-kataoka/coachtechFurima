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
            PaymentMethodsTableSeeder::class,
            UsersTableSeeder::class,
            CategoriesTableSeeder::class,
            ConditionsTableSeeder::class,
            ItemsTableSeeder::class,
            ItemCategoryConditionTableSeeder::class,
            LikesTableSeeder::class,
            UserItemListsTableSeeder::class,
        ]);
    }
}
