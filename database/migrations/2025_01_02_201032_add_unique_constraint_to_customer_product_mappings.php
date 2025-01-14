<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintToCustomerProductMappings extends Migration
{
    public function up()
    {
        Schema::table('customer_product_mappings', function (Blueprint $table) {
            // Add a unique constraint to the combination of customer_product_name and customer_id
            $table->unique(['customer_product_name', 'customer_id'], 'unique_customer_product_mapping');
        });
    }

    public function down()
    {
        Schema::table('customer_product_mappings', function (Blueprint $table) {
            // Drop the unique constraint
            $table->dropUnique('unique_customer_product_mapping');
        });
    }
}

