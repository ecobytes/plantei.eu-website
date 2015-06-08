<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPreferedLanguageToNewsletterSubscriptorsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('newsletter_subscriptors', function(Blueprint $table)
        {
			$table->string('prefered_language', 10)->after('verification_key');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('newsletter_subscriptors', function(Blueprint $table)
        {
			$table->dropColumn('prefered_language');

        });
    }

}
