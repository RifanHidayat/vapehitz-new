<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCentralSaleReturnProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('central_sale_return_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('central_sale_id');
            $table->foreignId('product_id');
            $table->integer('quantity');
            $table->string('cause', 50);
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
        Schema::dropIfExists('central_sale_return_product');
    }
}
