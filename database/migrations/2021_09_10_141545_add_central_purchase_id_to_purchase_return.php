<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCentralPurchaseIdToPurchaseReturn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->foreignId('central_purchase_id')->after('supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_returns', function (Blueprint $table) {
            //
            $table->dropColumn('central_purchase_id');
        });
    }
}
