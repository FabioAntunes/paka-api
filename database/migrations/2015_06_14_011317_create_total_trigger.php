<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTotalTrigger extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {

        DB::unprepared(
            'CREATE TRIGGER update_category_total
            AFTER INSERT ON `expenses` FOR EACH ROW
            BEGIN
            UPDATE categories set total=total+NEW.value
            WHERE id=NEW.category_id;
            END'
        );
    }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::unprepared('DROP TRIGGER `update_category_total`');
	}

}
