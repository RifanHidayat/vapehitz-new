<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetNullableToShippingCost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('central_sales', function (Blueprint $table) {
            $table->integer('shipping_cost')->nullable()->default(0)->change();
            $table->integer('other_cost')->nullable()->default(0)->change();
            $table->integer('deposit_customer')->nullable()->default(0)->change();
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
            $table->integer('shipping_cost')->nullable(false)->change();
            $table->integer('other_cost')->nullable(false)->change();
            $table->integer('deposit_customer')->nullable(false)->change();
        });
    }
}
