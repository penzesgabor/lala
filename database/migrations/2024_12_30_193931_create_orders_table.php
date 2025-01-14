<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('delivery_address_id');
            $table->date('ordering_date');
            $table->date('delivery_date')->nullable();
            $table->text('notes')->nullable();
            $table->date('production_date')->nullable();
            $table->boolean('isbilled')->default(false);
            $table->boolean('isdelivered')->default(false);
            $table->boolean('imported')->default(false);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('delivery_address_id')->references('id')->on('delivery_addresses')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
