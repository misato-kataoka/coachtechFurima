<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
        $table->id();

        // ★どの取引に対する評価か
        $table->foreignId('item_id')->constrained()->onDelete('cascade');

        // ★誰が評価したか (評価者)
        $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade');

        // ★誰が評価されたか (被評価者)
        $table->foreignId('evaluated_id')->constrained('users')->onDelete('cascade');

        // ★評価の種類 (5段階評価)
        $table->unsignedTinyInteger('rating');

        // ★評価コメント (任意)
        $table->text('comment')->nullable();

        $table->timestamps();

        // 同じ取引で、同じ人が同じ人を2回評価できないようにユニーク制約
        $table->unique(['item_id', 'evaluator_id', 'evaluated_id']);
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ratings');
    }
}
