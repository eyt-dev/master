<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('farms', function (Blueprint $table) {
            $table->string('mobile_number')->nullable()->after('type');
        });
    }

    public function down()
    {
        Schema::table('farms', function (Blueprint $table) {
            $table->dropColumn('mobile_number');
        });
    }
};
