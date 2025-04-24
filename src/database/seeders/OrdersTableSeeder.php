<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class OrdersTableSeeder extends Seeder
{
    /**
     * 実行メソッド
     *
     * @return void
     */
    public function run()
    {
        // buyer_id(=1)が購入者 / seller_id(=2)が出品者
        Order::create([
            'buyer_id'         => 1,
            'seller_id'        => 2,
            'item_id'          => 2,
            'payment_method_id'=> 1,
            'status'           => 'paid',
        ]);

        // buyer_id(=2)が購入者 / seller_id(=1)が出品者
        Order::create([
            'buyer_id'         => 2,
            'seller_id'        => 1,
            'item_id'          => 1,
            'payment_method_id'=> 2,
            'status'           => 'shipped',
        ]);
    }
}