<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerProductNameToOrderProductsTable extends Migration
{
    public function up()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->string('customer_product_name')->nullable()->after('barcode'); // Adds the customer_product_name column
        });
    }

    public function down()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn('customer_product_name'); // Removes the column
        });
    }
}
