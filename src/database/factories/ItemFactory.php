<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

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
            'user_id' => User::factory(),               // 出品者
            'buyer_id' => null,                         // デフォルトでは未購入
            'image' => 'images/dummy.jpg',              // ダミーの画像パス
            'item_name' => $this->faker->words(3, true),
            'brand' => $this->faker->company(),
            'price' => $this->faker->numberBetween(300, 50000),
            'description' => $this->faker->sentence(),
            'status' => 'on_sale',                        // デフォルトでは未購入
        ];
    }
}
