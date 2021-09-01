<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusCentralSales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('central_sales', function (Blueprint $table) {
            $table->string('status', 20)->nullable()->default("pending")->after('detail');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('central_sales', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
