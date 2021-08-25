<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCentralSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('central_sales', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('customer_id');
            $table->foreignId('shipment_id');
            $table->string('code', 50);
            $table->dateTime('date');
            $table->integer('debt');
            $table->decimal('total_weight');
            $table->integer('total_cost');
            $table->integer('discount')->nullable();
            $table->integer('subtotal');
            $table->integer('shipping_cost');
            $table->integer('other_cost');
            $table->string('detail_other_cost', 255)->nullable();
            $table->integer('deposit_customer');
            $table->integer('net_total');
            $table->integer('receipt_1');
            $table->integer('receive_1');
            $table->integer('receipt_2');
            $table->integer('receive_2');
            $table->integer('payment_amount');
            $table->integer('remaining_payment');
            $table->string('recipient', 255);
            $table->string('address_recipient', 255)->nullable();
            $table->string('detail', 255)->nullable();
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
        Schema::dropIfExists('central_sales');
    }
}
