<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPurchaseReceipt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_purchase_receipt', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('purchase_receipt_id');
            $table->foreignId('product_id');
            $table->integer('quantity')->default(0);
            $table->decimal('free')->default(0);
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
        Schema::dropIfExists('product_purchase_receipt');
    }
}
