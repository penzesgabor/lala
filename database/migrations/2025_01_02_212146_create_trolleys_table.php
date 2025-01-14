<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrolleysTable extends Migration
{
    public function up()
    {
        Schema::create('trolleys', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('space');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trolleys');
    }
}
