<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeedsVarityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variety', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name',100);
            $table->integer('species_id')->unsigned()->nullable();
            $table->foreign('species_id')->references('id')->on('species');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('variety');
    }
}
