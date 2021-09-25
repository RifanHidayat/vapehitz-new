<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditColumnDataTypeProductStudioSale extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_retail_sale', function (Blueprint $table) {
            $table->decimal('stock')->nullable()->change();
            // $table->decimal('price')->nullable()->change();
            $table->decimal('quantity')->nullable()->change();
            $table->decimal('free')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_retail_sale', function (Blueprint $table) {
            $table->integer('stock')->nullable()->change();
            // $table->integer('price')->nullable()->change();
            $table->integer('quantity')->nullable()->change();
            $table->integer('free')->nullable()->change();
        });
    }
}
