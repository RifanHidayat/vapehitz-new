<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnCentralSaleIdReturn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('central_sale_return_product', function (Blueprint $table) {
            $table->renameColumn('central_sale_id', 'central_sale_return_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('central_sale_return_product', function (Blueprint $table) {
            $table->renameColumn('central_sale_return_id', 'central_sale_id');
        });
    }
}
