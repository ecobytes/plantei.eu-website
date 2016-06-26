<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePituresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pictures', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('path',254);
            $table->string('url',254);
            $table->string('md5sum',32)->unique();
            $table->string('label',254)->nullable();
            $table->integer('seed_id')->unsigned()->nullable();
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
        //
        Schema::drop('pictures');
    }
}
