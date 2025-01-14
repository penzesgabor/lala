<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuttingListsTable extends Migration
{
    public function up()
    {
        Schema::create('cutting_lists', function (Blueprint $table) {
            $table->id();
            $table->integer('daily_number')->unsigned();
            $table->date('list_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cutting_lists');
    }
}

