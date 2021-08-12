<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInOutTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_out_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('account_in');
            $table->foreignId('account_out');
            $table->integer('number')->nullable();
            $table->date('date');
            $table->integer('amount')->nullable();
            $table->string('note', 255)->nullable();
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
        Schema::dropIfExists('in_out_transactions');
    }
}
