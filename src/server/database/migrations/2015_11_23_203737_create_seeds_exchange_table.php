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
            $table->integer('seed_id')->unsigned();
            $table->foreign('seed_id')->references('id')->on('seeds');
            $table->index(['asked_by', 'asked_to', 'seed_id']);

            // Should only be accepted (true) or refused (false)  by asked_to
            $table->boolean('accepted')->nullable();
            // Should only be completed by asked_by
            $table->boolean('completed')->default(false);
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
