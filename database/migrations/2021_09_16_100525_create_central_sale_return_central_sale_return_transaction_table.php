<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCentralSaleReturnCentralSaleReturnTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('central_sale_return_central_sale_return_transaction', function (Blueprint $table) {
            $table->id();
            $table->foreignId('central_sale_return_id');
            $table->foreignId('central_sale_return_transaction_id');
            $table->integer('amount');
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
        Schema::dropIfExists('central_sale_return_central_sale_return_transaction');
    }
}
