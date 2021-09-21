<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductStudioRequestToCentral extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_studio_request_to_central', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('studio_request_to_central_id');
            $table->foreignId('product_id');
            $table->decimal('central_stock')->default(0);
            $table->decimal('studio_stock')->default(0);
            $table->integer('quantity')->default(0);
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
        Schema::dropIfExists('product_studio_request_to_central');
    }
}
