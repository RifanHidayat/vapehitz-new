<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReturnQuantityToCentralPurchaseProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('central_purchase_product', function (Blueprint $table) {
            $table->integer('return_quantity')->after('quantity')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('central_purchase_product', function (Blueprint $table) {
            //
            $table->dropColumn('return_quantity');
        });
    }
}
