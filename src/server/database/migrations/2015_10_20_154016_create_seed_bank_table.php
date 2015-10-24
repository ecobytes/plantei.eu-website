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
            $table->smallInteger('origin')->default(0)->unsigned();// Barter(2); Bought(3); Own production(1)
            $table->text('description');
            $table->boolean('available');
            $table->boolean('public');
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
