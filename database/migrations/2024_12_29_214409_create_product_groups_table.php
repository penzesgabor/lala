<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('product_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('base_material_types_id');
            $table->timestamps();

            $table->foreign('base_material_types_id')->references('id')->on('base_material_types')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_groups');
    }
}
