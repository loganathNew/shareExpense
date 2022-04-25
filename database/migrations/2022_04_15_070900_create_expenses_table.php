<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('expense', 255);
            $table->integer('user_id')->unsigned();
            $table->enum('type', array('EQUAL', 'EXACT', 'PERCENT'));	
            $table->decimal('amount', 5, 2);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');

        });


        Schema::create('expense_splits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('expense_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('friend_id')->unsigned();
            $table->decimal('split_amount', 5, 2);
            $table->timestamps();
            $table->foreign('expense_id')->references('id')->on('expenses');
            $table->foreign('friend_id')->references('id')->on('users');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('expense_owes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('friend_id')->unsigned();
            $table->decimal('owe_amount', 5, 2);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('friend_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_splits');
        Schema::dropIfExists('expense_owes');

    }
}
