<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('buyer_id')->nullable()->constrained('users')->after('user_id')->onDelete('set null');
            $table->string('image');
            $table->string('item_name');
            $table->string('brand')->nullable();
            $table->decimal('price', 10, 2);
            $table->text('description');
            $table->boolean('is_sold')->default(false);
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
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('is_sold'); // is_soldカラムを削除
        });

        Schema::dropIfExists('items');
    }
}
