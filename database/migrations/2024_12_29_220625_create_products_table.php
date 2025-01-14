<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['simple', 'configurable']);
            $table->unsignedBigInteger('base_material_type_id')->nullable();
            $table->unsignedBigInteger('vat_id')->nullable();
            $table->unsignedBigInteger('product_group_id')->nullable();
            $table->string('english_name')->nullable();
            $table->float('weight_per_squaremeter')->nullable();
            $table->string('liseccode')->nullable();
            $table->timestamps();
        
            $table->foreign('base_material_type_id')->references('id')->on('base_material_types')->onDelete('cascade');
           # $table->foreign('vat_id')->references('id')->on('vats')->onDelete('cascade');
            $table->foreign('product_group_id')->references('id')->on('product_groups')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
