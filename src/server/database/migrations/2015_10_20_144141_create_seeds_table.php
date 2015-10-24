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
            $table->string('common_name');
            // Open (1)/ Closed (2) polinization
            $table->smallInteger('polinization')->default(0)->unsigned();
            // Direct planting (0) (1)false (2)true
            $table->smallInteger('direct')->default(0)->unsigned();
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
