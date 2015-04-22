<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('token_id')->unsigned()->index();
            $table->foreign('token_id')->references('id')->on('tokens');
            $table->integer('ip_addres')->unsigned();
            $table->string('request');
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
        Schema::drop('logs');
    }

}