<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeedMonthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seed_months', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('month')->unsigned();
            $table->integer('seed_id')->unsigned();
            $table->foreign('seed_id')->references('id')->on('seeds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('seed_months');
    }
}
