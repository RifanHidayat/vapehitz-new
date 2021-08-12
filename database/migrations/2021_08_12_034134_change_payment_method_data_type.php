<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePaymentMethodDataType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('central_purchases', function (Blueprint $table) {
            $table->string('payment_method', 30)->nullable()->default(null)->change();
            $table->foreignId('account_id')->nullable()->change();
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
            $table->integer('payment_method')->change();
            $table->foreignId('account_id')->change();
        });
    }
}
