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
        $data = [
            ['item_id' => 1, 'categories' => [1, 5], 'condition_id' => 3],
            ['item_id' => 2, 'categories' => [2],    'condition_id' => 3],
            ['item_id' => 3, 'categories' => [10],   'condition_id' => 1],
            ['item_id' => 4, 'categories' => [1, 5], 'condition_id' => 4],
            ['item_id' => 5, 'categories' => [2],    'condition_id' => 3],
            ['item_id' => 6, 'categories' => [2],    'condition_id' => 2],
            ['item_id' => 7, 'categories' => [1, 4], 'condition_id' => 4],
            ['item_id' => 8, 'categories' => [10],   'condition_id' => 3],
            ['item_id' => 9, 'categories' => [10],   'condition_id' => 2],
            ['item_id' => 10,'categories' => [4, 5, 6], 'condition_id' => 3],
        ];

        // 配列をループしてデータを投入
        foreach ($data as $row) {
            foreach ($row['categories'] as $categoryId) {
                ItemCategoryCondition::create([
                    'item_id'      => $row['item_id'],
                    'category_id'  => $categoryId,
                    'condition_id' => $row['condition_id'],
                ]);
            }
        }
    }
}