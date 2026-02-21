<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\Item;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        File::cleanDirectory(storage_path('app/public/images'));
        // コピー先のディレクトリを準備
        Storage::disk('public')->makeDirectory('images');
        Storage::disk('public')->makeDirectory('profiles');

        // コピー元のパス
        $sourcePath = resource_path('seed_images');
        $profileSourcePath = resource_path('seed_profiles');

        // コピー先のパス
        $destinationPath = storage_path('app/public/images');
        $profileDestinationPath = storage_path('app/public/profiles');
    
        // ファイルをコピー
        File::copyDirectory($sourcePath, $destinationPath);
        File::copyDirectory($profileSourcePath, $profileDestinationPath);

        Item::create([
                'user_id' => 1,
                'buyer_id' => 2,
                'image' => 'images/Armani+Mens+Clock.jpg',
                'item_name' => '腕時計',
                'brand' => 'アルマーニ',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'status' => 'on_sale',
            ]);
            Item::create([
                'user_id' => 2,
                'buyer_id' => null,
                'image' => 'images/HDD+Hard+Disk.jpg',
                'item_name' => 'HDD',
                'brand' => 'BUFFALO',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'status' => 'on_sale',
            ]);
            Item::create([
                'user_id' => 1,
                'buyer_id' => null,
                'image' => 'images/onion.jpg',
                'item_name' => '玉ねぎ3束',
                'brand' => 'JA',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'status' => 'on_sale',
            ]);
            Item::create([
                'user_id' => 1,
                'buyer_id' => null,
                'image' => 'images/LeatherShoes.jpg',
                'item_name' => '革靴',
                'brand' => 'リーガル',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'status' => 'on_sale',
            ]);
            Item::create([
                'user_id' => 2,
                'buyer_id' => null,
                'image' => 'images/Laptop.jpg',
                'item_name' => 'ノートPC',
                'brand' => 'DELL',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'status' => 'in_progress'
            ]);
            Item::create([
                'user_id' => 1,
                'buyer_id' => null,
                'image' => 'images/Mic.jpg',
                'item_name' => 'マイク',
                'brand' => 'YAMAHA',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'status' => 'on_sale',
            ]);
            Item::create([
                'user_id' => 2,
                'buyer_id' => null,
                'image' => 'images/Purse.jpg',
                'item_name' => 'ショルダーバッグ',
                'brand' => 'FURLA',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'status' => 'on_sale',
            ]);
            Item::create([
                'user_id' => 2,
                'buyer_id' => null,
                'image' => 'images/Tumbler.jpg',
                'item_name' => 'タンブラー',
                'brand' => 'サーモス',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'status' => 'on_sale',
            ]);
            Item::create([
                'user_id' => 1,
                'buyer_id' => null,
                'image' => 'images/CoffeeGrinder.jpg',
                'item_name' => 'コーヒーミル',
                'brand' => 'カリタ',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'status' => 'on_sale',
            ]);
            Item::create([
                'user_id' => 2,
                'buyer_id' => null,
                'image' => 'images/cosmetics.jpg',
                'item_name' => 'メイクセット',
                'brand' => '資生堂',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'status' => 'on_sale',
            ]);

    }
}