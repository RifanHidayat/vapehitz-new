<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSupplierIdNameCentralPurchases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('central_purchases', function (Blueprint $table) {
            $table->renameColumn('suppliers_id', 'supplier_id');
            $table->renameColumn('accounts_id', 'account_id');
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
            $table->renameColumn('supplier_id', 'suppliers_id');
            $table->renameColumn('account_id', 'accounts_id');
        });
    }
}
