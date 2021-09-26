<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsInitCentralSaleReturnTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('central_sale_return_transactions', function (Blueprint $table) {
            $table->tinyInteger('is_init')->nullable()->default(0)->after('note');
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
            $table->dropColumn(['is_init']);
        });
    }
}
