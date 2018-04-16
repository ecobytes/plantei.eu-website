<?php

use Cmgmyr\Messenger\Models\Models;
use Illuminate\Database\Migrations\Migration;

class AddNullableToLastReadInParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //DB::statement('ALTER TABLE `' . DB::getTablePrefix() . Models::table('participants') . '` CHANGE COLUMN `last_read` `last_read` timestamp NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP;');
     // DB::statement('ALTER TABLE `' . DB::getTablePrefix() . Models::table('participants') . '` ALTER COLUMN `last_read` SET NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
