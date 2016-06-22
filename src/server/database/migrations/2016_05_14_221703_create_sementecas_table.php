<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSementecasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sementecas', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('contact')->nullable();
            $table->string('address')->nullable();
            $table->float('lon')->nullable();
            $table->float('lat')->nullable();
            $table->string('name')->nullable();
            $table->text('descrition')->nullable();
        });
        Schema::create('sementecas_calendar', function (Blueprint $table) {
            $table->integer('sementeca_id')->unsigned();
            $table->foreign('sementeca_id')->references('id')->on('sementecas');
            $table->integer('calendar_id')->unsigned();
            $table->foreign('calendar_id')->references('id')->on('calendar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sementecas');
        Schema::drop('sementecas_calendar');
    }
}
