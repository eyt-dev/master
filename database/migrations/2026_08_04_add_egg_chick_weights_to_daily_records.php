<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('daily_records', function (Blueprint $table) {
            $table->decimal('eggs_weight', 10, 2)->nullable()->after('eggs_count');
            $table->decimal('chicks_weight', 10, 2)->nullable()->after('eggs_weight');
        });
    }

    public function down()
    {
        Schema::table('daily_records', function (Blueprint $table) {
            $table->dropColumn(['eggs_weight', 'chicks_weight']);
        });
    }
};
