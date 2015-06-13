<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFriendsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('friends', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('friendable_id')->unsigned()->index();
            $table->string('friendable_type');
            $table->string('name');
            $table->integer('version')->unsigned()->default(1);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('friends');
	}

}
