<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('expense_user', function(Blueprint $table)
		{
			$table->increments('id');
		 	$table->integer('expense_id')->unsigned()->index();
        	$table->foreign('expense_id')->references('id')->on('expenses');
		 	$table->integer('user_id')->unsigned()->index();
			$table->foreign('user_id')->references('id')->on('users');
			$table->boolean('is_owner');
			$table->tinyInteger('permissions');
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
		Schema::drop('expense_user');
	}

}
