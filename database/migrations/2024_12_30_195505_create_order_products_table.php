<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductsTable extends Migration
{
    public function up()
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id'); // Main product
            $table->unsignedBigInteger('divider_id')->nullable(); // Divider product
            $table->integer('height');
            $table->integer('width');
            $table->float('divider_length')->nullable();
            $table->integer('dividercross')->nullable();
            $table->integer('dividerend')->nullable();
            $table->boolean('gasfilling')->default(false);
            $table->float('extracharge')->default(0);
            $table->decimal('calculated_price', 10, 2)->default(0);
            $table->decimal('agreed_price', 10, 2)->default(0);
            $table->float('flowmeter')->default(0);
            $table->float('squaremeter')->default(0);
            $table->text('customers_order_text')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        
            // Foreign key constraints
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('divider_id')->references('id')->on('products')->onDelete('set null');
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('order_products');
    }
}
