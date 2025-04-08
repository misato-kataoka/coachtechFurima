<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Like;

class LikesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ユーザー1がアイテム2に「いいね」
        Like::create([
            'user_id' => 1,
            'item_id' => 2,
        ]);

        // ユーザー2がアイテム1に「いいね」
        Like::create([
            'user_id' => 2,
            'item_id' => 1,
        ]);
    }
}