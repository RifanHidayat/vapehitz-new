<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PaymentInitToCentralPurchasePurchaseTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('central_purchase_purchase_transaction', function (Blueprint $table) {
            //
            $table->string('payment_init')->default(0)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('central_purchase_purchase_transaction', function (Blueprint $table) {
            //
            $table->dropColumn('payment_init');
        });
    }
}
