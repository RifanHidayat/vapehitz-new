<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetNullableToReceiptAndReceive extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('central_sales', function (Blueprint $table) {
            $table->integer('receipt_1')->nullable()->change();
            $table->integer('receive_1')->nullable()->change();
            $table->integer('receipt_2')->nullable()->change();
            $table->integer('receive_2')->nullable()->change();
            $table->string('recipient', 255)->nullable()->change();
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
            $table->integer('receipt_1')->nullable(false)->change();
            $table->integer('receive_1')->nullable(false)->change();
            $table->integer('receipt_2')->nullable(false)->change();
            $table->integer('receive_2')->nullable(false)->change();
            $table->string('recipient', 255)->nullable(false)->change();
        });
    }
}
