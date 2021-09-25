<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PaymentInitToPurchaseTransactions extends Migration
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
            $table->string('payment_init')->default(0)->after('payment_method');
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
            $table->dropColumn('payment_init');
        });
    }
}
