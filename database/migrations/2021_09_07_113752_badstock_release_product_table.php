<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BadstockReleaseProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('badstock_release_product', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('product_id');
            $table->foreignId('badstock_release_id');
            $table->decimal('bad_stock')->default(0);
            $table->string('quantity')->default(0);
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
        Schema::dropIfExists('badstock_release_product');
    }
}
