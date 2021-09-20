<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStudioSaleReturnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_studio_sale_return', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studio_sale_return_id');
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
        Schema::dropIfExists('product_studio_sale_return');
    }
}
