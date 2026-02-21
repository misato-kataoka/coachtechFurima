<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $methods = [
            [
                'id' => 1, 
                'payment_method' => 'クレジットカード',
            ],
            [
                'id' => 2, 
                'payment_method' => 'コンビニ払い',
            ],
        ];

        $now = Carbon::now();

        foreach ($methods as $method) {
            DB::table('payment_methods')->insert([
                'id' => $method['id'],
                'payment_method' => $method['payment_method'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}