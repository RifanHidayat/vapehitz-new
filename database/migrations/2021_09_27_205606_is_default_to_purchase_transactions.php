<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IsDefaultToPurchaseTransactions extends Migration
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
            $table->string('is_default')->default(0)->after('central_purchase_id');
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
            Schema::dropIfExists('id_default');
        });
    }
}
