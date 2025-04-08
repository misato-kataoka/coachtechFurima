<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItemCategoryCondition;

class ItemCategoryConditionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ItemCategoryCondition::create([
            'item_id'     => 1,
            'category_id' => 5,
            'condition_id'=> 3,
        ]);

        ItemCategoryCondition::create([
            'item_id'     => 2,
            'category_id' => 2,
            'condition_id'=> 3,
        ]);

        ItemCategoryCondition::create([
            'item_id'     => 3,
            'category_id' => 10,
            'condition_id'=> 1,
        ]);

        ItemCategoryCondition::create([
            'item_id'     => 4,
            'category_id' => 5,
            'condition_id'=> 4,
        ]);

        ItemCategoryCondition::create([
            'item_id'     => 5,
            'category_id' => 2,
            'condition_id'=> 3,
        ]);

        ItemCategoryCondition::create([
            'item_id'     => 6,
            'category_id' => 2,
            'condition_id'=> 2,
        ]);

        ItemCategoryCondition::create([
            'item_id'     => 7,
            'category_id' => 1,
            'condition_id'=> 4,
        ]);

        ItemCategoryCondition::create([
            'item_id'     => 8,
            'category_id' => 10,
            'condition_id'=> 3,
        ]);

        ItemCategoryCondition::create([
            'item_id'     => 9,
            'category_id' => 10,
            'condition_id'=> 2,
        ]);

        ItemCategoryCondition::create([
            'item_id'     => 10,
            'category_id' => 6,
            'condition_id'=> 3,
        ]);
    }
}
