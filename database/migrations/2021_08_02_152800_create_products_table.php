<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('product_category_id');
            $table->foreignId('product_subcategory_id');
            $table->string('code', 20)->nullable();
            $table->string('name', 255);
            $table->decimal('weight')->nullable()->default(0);
            $table->decimal('central_stock')->nullable()->default(0);
            $table->decimal('retail_stock')->nullable()->default(0);
            $table->decimal('studio_stock')->nullable()->default(0);
            $table->decimal('bad_stock')->nullable()->default(0);
            $table->integer('purchase_price')->nullable()->default(0);
            $table->integer('agent_price')->nullable()->default(0);
            $table->integer('ws_price')->nullable()->default(0);
            $table->integer('retail_price')->nullable()->default(0);
            $table->tinyInteger('status')->nullable()->default(1);
            $table->tinyInteger('is_changeable')->nullable()->default(0);
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
        Schema::dropIfExists('products');
    }
}
