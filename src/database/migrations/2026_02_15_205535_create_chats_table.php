<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
        $table->id();
        $table->foreignId('item_id')->constrained()->onDelete('cascade'); // どの商品に対するチャットか
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 誰が投稿したか
        $table->text('message'); // メッセージ本文
        $table->string('image_path')->nullable(); // 画像のパス
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
        Schema::dropIfExists('chats');
    }
}
