<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseInviteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('expense_invite', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('expense_id')->unsigned()->index();
            $table->foreign('expense_id')->references('id')->on('expenses');
            $table->integer('invite_id')->unsigned()->index();
            $table->foreign('invite_id')->references('id')->on('invites');
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
        Schema::drop('expense_invite');
	}

}
