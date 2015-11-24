<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('reply_to')->unsigned()->nullable();
            $table->foreign('reply_to')->references('id')->on('messages');
            $table->integer('root_message_id')->unsigned()->nullable();
            $table->foreign('root_message_id')->references('id')->on('messages');
            $table->string('subject');
            $table->text('body');
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
        Schema::drop('messages');
    }
}
