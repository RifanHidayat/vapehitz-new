<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCentralSaleReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('central_sale_returns', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20);
            $table->date('date');
            $table->foreignId('central_sale_id');
            $table->foreignId('account_id');
            $table->foreignId('customer_id');
            $table->string('payment_method', 50);
            $table->integer('quantity');
            $table->bigInteger('amount');
            $table->string('note', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('central_sale_returns');
    }
}
