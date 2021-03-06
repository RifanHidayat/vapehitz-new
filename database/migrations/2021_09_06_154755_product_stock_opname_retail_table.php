<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductStockOpnameRetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stock_opname_retail', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('product_id');
            $table->foreignId('stock_opname_retail_id');
            $table->decimal('good_stock')->default(0);
            $table->string('description')->default(0);
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
        Schema::dropIfExists('product_stock_opname_retail');
    }
}
