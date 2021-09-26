<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetailSaleTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_sale_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20);
            $table->date('date');
            $table->foreignId('account_id');
            // $table->foreignId('customer_id');
            $table->bigInteger('amount');
            $table->string('payment_method', 50);
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
        Schema::dropIfExists('retail_sale_transactions');
    }
}
