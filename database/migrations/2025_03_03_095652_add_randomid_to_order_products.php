<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->string('randomid', 9)->nullable()->after('id'); // Add new field
        });
    }

    public function down()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn('randomid'); // Rollback
        });
    }
};
