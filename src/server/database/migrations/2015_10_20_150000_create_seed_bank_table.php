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
        Schema::create('seeds', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('local',100);
            $table->smallInteger('year')->unsigned();
            //$table->smallInteger('origin')->default(0)->unsigned();// Barter(2); Bought(3); Own production(1)
            $table->text('description');
            $table->boolean('available');
            $table->boolean('public');
            $table->string('common_name');
            $table->string('latin_name');
            $table->integer('species_id')->unsigned()->nullable();
            $table->foreign('species_id')->references('id')->on('species');
            $table->integer('variety_id')->unsigned()->nullable();
            $table->foreign('variety_id')->references('id')->on('variety');
            $table->integer('family_id')->unsigned()->nullable();
            $table->foreign('family_id')->references('id')->on('family');
            // Open (1)/ Closed (2) polinization
            $table->smallInteger('polinization')->default(0)->unsigned();
            // Direct planting (0) (1)false (2)true
            $table->smallInteger('direct')->default(0)->unsigned();
        });
        Schema::create('seeds_cooking', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('seed_id')->unsigned();
            $table->foreign('seed_id')->references('id')
                ->on('seeds')->onDelete('cascade');
            $table->text('recipe');
        });
        Schema::create('seeds_medicine', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('seed_id')->unsigned();
            $table->foreign('seed_id')->references('id')
                ->on('seeds')->onDelete('cascade');
            $table->text('uses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('seeds_cooking');
        Schema::drop('seeds');
    }
}
