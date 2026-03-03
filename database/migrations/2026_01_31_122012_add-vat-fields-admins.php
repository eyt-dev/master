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
        Schema::table('admins', function (Blueprint $table) {
            $table->string('vat_country_code', 4)->nullable()->after('username');
            $table->string('vat_number')->nullable()->after('vat_country_code');
            $table->integer('created_from')->default(1)->after('vat_number')->comment('1:web,2:register');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['vat_country_code', 'vat_number', 'created_from']);
        });
    }
};
