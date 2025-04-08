<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Item::create([
                'user_id' => 1,
                'image' => 'storage/images/Armani+Mens+Clock.jpg',
                'product_name' => '腕時計',
                'brand' => 'アルマーニ',
                'price' => '15000',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
            ]);
            Item::create([
                'user_id'       => 2,
                'image' => 'storage/images/HDD+Hard+Disk.jpg',
                'product_name' => 'HDD',
                'brand' => 'BUFFALO',
                'price' => '5000',
                'description' => '高速で信頼性の高いハードディスク',
            ]);
            Item::create([
                'user_id' => 1,
                'image' => 'storage/images/onion.jpg',
                'product_name' => '玉ねぎ3束',
                'brand' => 'JA',
                'price' => '300',
                'description' => '新鮮な玉ねぎ3束のセット',
            ]);
            Item::create([
                'user_id' => 1,
                'image' => 'storage/images/LeatherShoes.jpg',
                'product_name' => '革靴',
                'brand' => 'リーガル',
                'price' => '4000',
                'description' => 'クラシックなデザインの革靴',
            ]);
            Item::create([
                'user_id' => 2,
                'image' => 'storage/images/Laptop.jpg',
                'product_name' => 'ノートPC',
                'brand' => 'DELL',
                'price' => '45000',
                'description' => '高性能なノートパソコン',
            ]);
            Item::create([
                'user_id' => 1,
                'image' => 'storage/images/Mic.jpg',
                'product_name' => 'マイク',
                'brand' => 'YAMAHA',
                'price' => '8000',
                'description' => '高音質のレコーディング用マイク',
            ]);
            Item::create([
                'user_id' => 2,
                'image' => 'storage/images/Purse.jpg',
                'product_name' => 'ショルダーバッグ',
                'brand' => 'FURLA',
                'price' => '3500',
                'description' => 'おしゃれなショルダーバッグ',
            ]);
            Item::create([
                'user_id' => 2,
                'image' => 'storage/images/Tumbler.jpg',
                'product_name' => 'タンブラー',
                'brand' => 'サーモス',
                'price' => '500',
                'description' => '使いやすいタンブラー',
            ]);
            Item::create([
                'user_id' => 1,
                'image' => 'storage/images/CoffeeGrinder.jpg',
                'product_name' => 'コーヒーミル',
                'brand' => 'カリタ',
                'price' => '4000',
                'description' => '手動のコーヒーミル',
            ]);
            Item::create([
                'user_id' => 2,
                'image' => 'storage/images/cosmetics.jpg',
                'product_name' => 'メイクセット',
                'brand' => '資生堂',
                'price' => '2500',
                'description' => '便利なメイクアップ背tメイクアップセット',
            ]);

    }
}