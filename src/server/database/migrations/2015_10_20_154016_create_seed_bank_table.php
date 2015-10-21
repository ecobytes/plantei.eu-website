<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeedBankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seeds_bank', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('seed_id')->unsigned();
            $table->foreign('seed_id')->references('id')->on('seeds');
            $table->string('local',100);
            $table->smallInteger('year')->unsigned();
            $table->smallInteger('origin')->unsigned();// Barter(1); Bought(2); Own production(0)
            $table->text('description');
            $table->boolean('available');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('seeds_bank');
    }
}
