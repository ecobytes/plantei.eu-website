<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Seedstableaddnullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //$table->increments('id');
        Schema::table('seeds', function ($table) {
            $table->string('local',100)->nullable()->change();
            $table->smallInteger('year')->unsigned()->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->boolean('available')->default(true)->change();
            $table->boolean('public')->default(true)->change();
            $table->string('latin_name')->nullable()->change();
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
    }
}
