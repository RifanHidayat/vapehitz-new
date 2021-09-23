<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedUpdatedByCentralPurchases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('central_purchases', function (Blueprint $table) {
            $table->after('updated_at', function (Blueprint $table) {
                $table->foreignId('created_by')->nullable();
                $table->foreignId('updated_by')->nullable();
            });
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
            $table->dropColumn(['created_by', 'updated_by']);
        });
    }
}
