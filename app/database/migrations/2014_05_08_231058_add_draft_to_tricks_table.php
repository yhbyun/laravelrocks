<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDraftToTricksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tricks', function(Blueprint $table) {
            $table->tinyInteger('draft')->default(0)->after('user_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tricks', function(Blueprint $table) {
            $table->dropColumn('draft');
		});
	}

}
