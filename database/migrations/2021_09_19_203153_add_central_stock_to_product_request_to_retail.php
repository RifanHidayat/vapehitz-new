<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCentralStockToProductRequestToRetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_request_to_retail', function (Blueprint $table) {
            $table->decimal('central_stock')->after('retail_stock')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_request_to_retail', function (Blueprint $table) {
            $table->dropColumn('central_stock');
        });
    }
}
