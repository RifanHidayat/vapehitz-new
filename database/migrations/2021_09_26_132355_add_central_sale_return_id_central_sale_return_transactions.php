<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCentralSaleReturnIdCentralSaleReturnTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('central_sale_transactions', function (Blueprint $table) {
            $table->foreignId('central_sale_return_id')->nullable()->after('is_init');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('central_sale_transactions', function (Blueprint $table) {
            $table->dropColumn(['central_sale_return_id']);
        });
    }
}
