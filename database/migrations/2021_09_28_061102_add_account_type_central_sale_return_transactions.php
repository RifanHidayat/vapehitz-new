<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountTypeCentralSaleReturnTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('central_sale_return_transactions', function (Blueprint $table) {
            $table->string('account_type', 10)->nullable()->after('account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('central_sale_return_transactions', function (Blueprint $table) {
            $table->dropColumn(['account_type']);
        });
    }
}
