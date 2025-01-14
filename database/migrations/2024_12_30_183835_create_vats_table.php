<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVatsTable extends Migration
{
    public function up()
    {
        Schema::create('vats', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // VAT name, e.g., "Standard", "Reduced"
            $table->decimal('value', 5, 2);  // VAT value, e.g., 5.00, 20.00
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vats');
    }
}
