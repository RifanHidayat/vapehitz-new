<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCentralPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('central_purchases', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('suppliers_id');
            $table->foreignId('accounts_id');
            $table->string('code', 50);
            $table->date('date');
            $table->integer('total')->nullable()->default(0);
            $table->integer('shipping_cost')->nullable()->default(0);
            $table->integer('discount')->nullable()->default(0);
            $table->integer('netto')->nullable()->default(0);
            $table->integer('pay_amount')->nullable()->default(0);
            $table->integer('payment_method')->nullable()->default(0);
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
        Schema::dropIfExists('central_purchases');
    }
}
