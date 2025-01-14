<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city');
            $table->string('street');
            $table->string('zip');
            $table->string('phone');
            $table->string('contact_name');
            $table->string('bank_account_nr');
            $table->string('tax_number');
            $table->string('booking_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
