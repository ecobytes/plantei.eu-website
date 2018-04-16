<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeedbankTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

      Schema::create('family', function (Blueprint $table) {
          $table->increments('id');
          $table->timestamps();
          $table->string('name',100);
      });

      Schema::create('species', function (Blueprint $table) {
          $table->increments('id');
          $table->timestamps();
          $table->string('name',100);
          $table->integer('family_id')->unsigned()->nullable();
          $table->foreign('family_id')->references('id')->on('family');
      });

      Schema::create('variety', function (Blueprint $table) {
          $table->increments('id');
          $table->timestamps();
          $table->string('name',100);
          $table->integer('species_id')->unsigned()->nullable();
          $table->foreign('species_id')->references('id')->on('species');
      });

      Schema::create('seeds', function (Blueprint $table) {
          $table->increments('id');
          $table->timestamps();
          $table->integer('user_id')->unsigned();
          $table->foreign('user_id')->references('id')->on('users');

          // Main description
          $table->string('common_name');
          $table->string('latin_name')->nullable();
          $table->integer('species_id')->unsigned()->nullable();
          $table->foreign('species_id')->references('id')->on('species');
          $table->integer('variety_id')->unsigned()->nullable();
          $table->foreign('variety_id')->references('id')->on('variety');
          $table->integer('family_id')->unsigned()->nullable();
          $table->foreign('family_id')->references('id')->on('family');
          $table->smallInteger('year')->unsigned()->nullable();
          $table->string('local',100)->nullable();
          $table->boolean('available')->default(true);
          $table->string('units',20)->nullable();
          $table->integer('quantity')->nullable()->unsigned();
          // Barter(2); Bought(3); Own production(1)
          $table->smallInteger('origin')->default(0)->unsigned();
          $table->boolean('traditional')->default(false);
          $table->integer('risk')->nullable()->unsigned();

          // Farming
          // Months until recollection
          $table->integer('untilharvest')->nullable()->unsigned();
          // Direct planting (0) (1)false (2)true
          $table->smallInteger('direct')->default(0)->unsigned();
          $table->text('description')->nullable();

          $table->boolean('public')->default(true);
          // Open (1)/ Closed (2) polinization
          $table->smallInteger('polinization')->default(0)->unsigned();
      });

      // TODO: Remove this use Seed_Uses from enciclopedia
      Schema::create('seeds_ends', function (Blueprint $table) {
        $table->increments('id');
        $table->timestamps();
        $table->integer('seed_id')->unsigned();
        $table->foreign('seed_id')->references('id')
            ->on('seeds')->onDelete('cascade');
        $table->text('article');
        // medicinal, textil, cooking, other, dangers?
        $table->string('category');
      });

      Schema::create('seeds_months', function (Blueprint $table) {
        //$table->increments('id');
        //$table->timestamps();
        $table->smallInteger('month')->unsigned();
        $table->integer('seed_id')->unsigned();
        $table->foreign('seed_id')->references('id')->on('seeds');
        $table->index(['month', 'seed_id']);
      });

      Schema::create('seeds_exchanges', function (Blueprint $table) {
        $table->increments('id');
        $table->timestamps();
        $table->integer('asked_by')->unsigned();
        $table->foreign('asked_by')->references('id')->on('users');
        $table->integer('asked_to')->unsigned();
        $table->foreign('asked_to')->references('id')->on('users');
        $table->integer('seed_id')->unsigned()->nullable();
        $table->foreign('seed_id')->references('id')->on('seeds');
        // When an exchange includes more than one seed it will have a parent exchange,
        // that has no seed associated, to keep them together. Accepted will have no meaning,
        // and completed will only exist when all seeds exchanges have been completed
        $table->integer('parent_id')->unsigned()->nullable();
        $table->foreign('parent_id')->references('id')->on('seeds_exchanges');

        // Should only be accepted (2) or refused (1)  by asked_to
        $table->integer('accepted')->unsigned()->default(0);
        // Should only be completed (2) or rejected (1) by asked_by
        $table->integer('completed')->unsigned()->default(0);
      });

      Schema::create('pictures', function (Blueprint $table) {
        $table->increments('id');
        $table->timestamps();
        $table->string('path',254);
        $table->string('url',254);
        $table->string('md5sum',32)->nullable();
        $table->string('label',254)->nullable();
        $table->integer('seed_id')->unsigned()->nullable();
        $table->foreign('seed_id')->references('id')->on('seeds');
        // https://laravel.com/docs/5.1/eloquent-relationships#polymorphic-relations
        $table->integer('imageable_id')->nullable();
        $table->string('imageable_type')->nullable();
      });

      Schema::create('calendar', function (Blueprint $table) {
          $table->increments('id');
          $table->timestamps();
          $table->integer('user_id')->unsigned();
          $table->foreign('user_id')->references('id')->on('users');
          $table->dateTime('start');
          $table->dateTime('end');
          $table->string('category')->nullable();
          $table->string('address')->nullable();
          $table->string('location')->nullable();
          $table->float('lon')->nullable();
          $table->float('lat')->nullable();
          $table->string('title');
          $table->text('description')->nullable();
      });

      Schema::create('sementecas', function (Blueprint $table) {
          $table->increments('id');
          $table->timestamps();
          $table->string('contact')->nullable();
          $table->string('address')->nullable();
          $table->float('lon')->nullable();
          $table->float('lat')->nullable();
          $table->string('name')->nullable();
          $table->text('description')->nullable();
          $table->boolean('active')->default(false);
      });

      Schema::create('sementecas_calendar', function (Blueprint $table) {
          $table->integer('sementeca_id')->unsigned();
          $table->foreign('sementeca_id')->references('id')->on('sementecas');
          $table->integer('calendar_id')->unsigned();
          $table->foreign('calendar_id')->references('id')->on('calendar');
      });

      Schema::create('contacts', function (Blueprint $table) {
        //$table->increments('id');
        $table->integer('user_id')->unsigned();
        $table->integer('contact_id')->unsigned();
        $table->primary(['user_id', 'contact_id']);
      });

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
          $table->integer('popnameable_id');
          $table->integer('popnameble_type');
      });

      Schema::create('references', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('referenceable_id')->nullable();
          $table->string('referenceable_type')->nullable();
          $table->integer('type');
          $table->string('content');
      });

      Schema::create('plantuses', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('plantusesable_id')->nullable();
          $table->string('plantusesable_type')->nullable();
          $table->string('title')->nullable();
          $table->text('article');
          // [ "alimentar", "medicinal", "artesanal", "auxiliar, horta ou casa",
          //   "tóxico ou nocivo", "social, simbólico, ritual", "outros usos especiais"]
          $table->integer('category_id');
      });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('plantuses');
      Schema::drop('references');
      Schema::drop('popnames');
      Schema::drop('encilopedias');
      Schema::drop('contacts');
      Schema::drop('sementecas_calendar');
      Schema::drop('sementecas');
      Schema::drop('calendar');
      Schema::drop('pictures');
      Schema::drop('seeds_exchanges');
      Schema::drop('seeds_months');
      // TODO: Remove seeds_ends use plantuses
      Schema::drop('seeds_ends');
      Schema::drop('seeds');
      Schema::drop('variety');
      Schema::drop('species');
      Schema::drop('family');
    }
}
