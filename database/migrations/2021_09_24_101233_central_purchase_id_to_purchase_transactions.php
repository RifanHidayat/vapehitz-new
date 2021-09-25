<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CentralPurchaseIdToPurchaseTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_transactions', function (Blueprint $table) {
            //
            $table->integer('central_purchase_id')->default(0)->after('supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_transactions', function (Blueprint $table) {
            //
            $table->dropColumn('central-purchase_id');
        });
    }
}
