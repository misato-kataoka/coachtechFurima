<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 購入者
        $table->foreignId('item_id')->constrained()->onDelete('cascade'); // 商品

        $table->foreignId('payment_method_id')->constrained('payment_methods');

        $table->integer('amount');
        $table->string('shipping_postal_code');
        $table->string('shipping_address');
        $table->string('shipping_building')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
