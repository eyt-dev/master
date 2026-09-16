<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('material_names', function (Blueprint $table) {
            $table->enum('type', ['Pelleted feed', 'Feedstuff'])->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('material_names', function (Blueprint $table) {
            $table->dropColumn(['type', 'created_by']);
        });
    }
};
