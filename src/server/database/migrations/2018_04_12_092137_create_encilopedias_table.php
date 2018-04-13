<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEncilopediasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encilopedias', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('description');
            $table->string('common_name');
            $table->string('latin_name');
            $table->integer('family_id')->unsigned()->nullable();
            $table->foreign('family_id')->references('id')->on('family');
        });
        Schema::create('popnames', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pop_name');
            $table->integer('model_id');
            $table->integer('instance_id');
        });
        Schema::create('references', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('enciclopedia_id ');
            $table->foreign('enciclopedia_id')->references('id')->on('enciclopedia');
            $table->integer('type');
            $table->string('content');
        });
        Schema::create('plantuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('enciclopedia_id ');
            $table->foreign('enciclopedia_id')->references('id')->on('enciclopedia');
            $table->text('article');
            // [ "alimentar", "medicinal", "artesanal", "auxiliar, horta ou casa",
            //   "tóxico ou nocivo", "social, simbólico, ritual", "outros usos especiais"]
            $table->string('category_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('encilopedias');
      Schema::drop('popnames');
      Schema::drop('references');
      Schema::drop('plantusages');
    }
}
