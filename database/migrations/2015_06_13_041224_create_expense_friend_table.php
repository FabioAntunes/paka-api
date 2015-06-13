<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseFriendTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('expense_friend', function(Blueprint $table)
        {
            $table->increments('id');
            $table->foreign('expense_id')->references('id')->on('expenses');
            $table->integer('expense_id')->unsigned()->index();
            $table->foreign('friend_id')->references('id')->on('friends');
            $table->integer('friend_id')->unsigned()->index();
            $table->decimal('value', 10, 2);
            $table->boolean('is_paid');
            $table->integer('version')->unsigned()->default(1);
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
        Schema::drop('expense_friend');
    }

}
