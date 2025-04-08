<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentMethod::create([
            'payment_method' => 'コンビニ払い',
        ]);

        PaymentMethod::create([
            'payment_method' => 'カード支払い',
        ]);
    }
}