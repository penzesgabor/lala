<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuttingSelectionsTable extends Migration
{
    public function up()
    {
        Schema::create('cutting_selections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_product_id');
            $table->unsignedBigInteger('cutting_list_id');
            $table->unsignedBigInteger('trolley_id')->nullable();
            $table->timestamps();

            $table->foreign('order_product_id')->references('id')->on('order_products')->onDelete('cascade');
            $table->foreign('cutting_list_id')->references('id')->on('cutting_lists')->onDelete('cascade');
            $table->foreign('trolley_id')->references('id')->on('trolleys')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cutting_selections');
    }
}
