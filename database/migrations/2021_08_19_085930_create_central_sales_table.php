<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCentralSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('central_sales', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('customer_id');
            $table->foreignId('shipment_id');
            $table->string('code', 50);
            $table->dateTime('date');
            $table->integer('debt');
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
        Schema::dropIfExists('central_sales');
    }
}