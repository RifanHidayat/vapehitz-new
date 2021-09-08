<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusBadstockReleases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('badstock_releases', function (Blueprint $table) {
            $table->string('status', 20)->nullable()->default("pending")->after('image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('badstock_releases', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
