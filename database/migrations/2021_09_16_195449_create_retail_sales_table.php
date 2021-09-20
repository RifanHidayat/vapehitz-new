<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetailSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_sales', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('customer_id');
            $table->string('code', 50);
            $table->dateTime('date');
            $table->foreignId('shipment_id')->nullable();
            // $table->integer('debt');
            $table->decimal('total_weight');
            // $table->integer('total_cost');
            $table->integer('shipping_cost')->nullable()->default(0);
            $table->integer('discount')->nullable()->default(0);
            $table->string('discount_type', 20)->nullable()->default('nominal');
            $table->integer('other_cost')->nullable()->default(0);
            $table->string('detail_other_cost', 255)->nullable();
            $table->integer('subtotal');
            $table->integer('payment_amount');
            // $table->integer('deposit_customer');
            $table->integer('net_total');
            $table->string('payment_method', 20)->nullable();
            $table->foreignId('account_id', 20)->nullable();
            // $table->integer('receipt_1');
            // $table->integer('receive_1');
            // $table->integer('receipt_2');
            // $table->integer('receive_2');
            // $table->integer('remaining_payment');
            // $table->string('recipient', 255);
            // $table->string('address_recipient', 255)->nullable();
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
        Schema::dropIfExists('retail_sales');
    }
}
