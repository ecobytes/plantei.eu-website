<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeedsExchangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seeds_exchanges', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('asked_by')->unsigned();
            $table->foreign('asked_by')->references('id')->on('users');
            $table->integer('asked_to')->unsigned();
            $table->foreign('asked_to')->references('id')->on('users');
            $table->integer('seed_id')->unsigned()->nullable();
            $table->foreign('seed_id')->references('id')->on('seeds');
            // When an exchange includes more than one seed it will have a parent exchange,
            // that has no seed associated, to keep them together. Accepted will have no meaning,
            // and completed will only exist when all seeds exchanges have been completed
            $table->integer('parent_id')->unsigned()->nullable();
            $table->foreign('parent_id')->references('id')->on('seeds_exchanges');

            // Should only be accepted (2) or refused (1)  by asked_to
            $table->integer('accepted')->unsigned()->default(0);
            // Should only be completed (2) or rejected (1) by asked_by
            $table->integer('completed')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('seeds_exchanges');
    }
}
