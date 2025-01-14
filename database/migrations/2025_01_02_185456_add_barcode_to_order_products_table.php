<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBarcodeToOrderProductsTable extends Migration
{
    public function up()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->string('barcode')->nullable()->after('notes'); // Adds the barcode column
        });
    }

    public function down()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn('barcode'); // Removes the barcode column
        });
    }
}
