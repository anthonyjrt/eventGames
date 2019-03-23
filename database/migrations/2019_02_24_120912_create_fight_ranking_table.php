<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFightRankingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fight_ranking', function (Blueprint $table) {
            $table->integer('fight_id')->unsigned();
            $table->integer('ranking_id')->unsigned();
            $table->integer('color');

            $table->foreign('fight_id')->references('id')->on('fights')->onDelete('cascade');
            $table->foreign('ranking_id')->references('id')->on('rankings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fight_ranking');
    }
}
