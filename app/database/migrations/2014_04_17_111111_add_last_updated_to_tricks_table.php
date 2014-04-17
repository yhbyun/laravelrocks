<?php

use Illuminate\Database\Migrations\Migration;

class AddLastUpdatedToTricksTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tricks', function($table) {
            $table->timestamp('last_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tricks', function($table) {
            $table->dropColumn('last_updated_at');
        });
    }

}