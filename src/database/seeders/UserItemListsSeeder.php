<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserItemList; // UserItemListモデルを使用

class UserItemListsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $favorites = [
            ['user_id' => 1, 'item_id' => 2],
            ['user_id' => 2, 'item_id' => 4],
            ['user_id' => 1, 'item_id' => 10],
        ];

        // 配列をループしてデータを投入
        foreach ($favorites as $favorite) {
            UserItemList::create($favorite);
        }
    }
}