<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserFriendTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_friend', function(Blueprint $table)
		{
			$table->increments('id');
		 	$table->integer('user_id')->unsigned()->index();
			$table->foreign('user_id')->references('id')->on('users');
		 	$table->integer('friend_id')->unsigned()->index();
        	$table->foreign('friend_id')->references('id')->on('users');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_friend');
	}

}
