<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seeds', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('sci_name');
            // Open (true)/ Closed (false) polinization
            $table->boolean('polinization');
            // Direct planting
            $table->boolean('direct');
            $table->integer('species_id')->unsigned()->nullable();
            $table->foreign('species_id')->references('id')->on('species');
            $table->integer('variety_id')->unsigned()->nullable();
            $table->foreign('variety_id')->references('id')->on('variety');
            $table->integer('family_id')->unsigned()->nullable();
            $table->foreign('family_id')->references('id')->on('family');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('seeds');
    }
}
