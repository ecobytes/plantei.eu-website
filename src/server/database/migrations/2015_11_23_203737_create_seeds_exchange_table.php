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

            // Should only be accepted (true) or refused (false)  by asked_to
            $table->boolean('accepted')->nullable();
            // Should only be completed (true) or rejected (false) by asked_by
            $table->boolean('completed')->nullable();
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
