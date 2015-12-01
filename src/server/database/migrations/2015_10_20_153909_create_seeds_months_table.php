<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeedsMonthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seeds_months', function (Blueprint $table) {
            //$table->increments('id');
            //$table->timestamps();
            $table->smallInteger('month')->unsigned();
            $table->integer('seed_id')->unsigned();
            $table->foreign('seed_id')->references('id')->on('seeds');
            $table->index(['month', 'seed_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('seeds_months');
    }
}
