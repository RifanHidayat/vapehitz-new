<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceNumberAndInvoiceDateToCentralPurchases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('central_purchases', function (Blueprint $table) {
            //
            $table->string('invoice_number',100)->after('id');
            $table->string('username')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('central_purchases', function (Blueprint $table) {
            //
        });
    }
}
