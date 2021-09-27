<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudioSaleStudioSaleTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('studio_sale_studio_sale_transaction', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studio_sale_id');
            $table->foreignId('studio_sale_transaction_id');
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
        Schema::dropIfExists('studio_sale_studio_sale_transaction');
    }
}
