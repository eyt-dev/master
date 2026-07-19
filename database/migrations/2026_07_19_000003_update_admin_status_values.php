<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('status', 20)->default('Active')->change();
        });

        DB::table('admins')->whereIn('status', ['Enable', 'Disable', 'Pending'])->update([
            'status' => DB::raw("CASE WHEN status = 'Enable' THEN 'Active' WHEN status = 'Disable' THEN 'Inactive' ELSE 'Active' END"),
        ]);
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->enum('status', ['Enable', 'Disable', 'Pending'])->default('Pending')->change();
        });
    }
};
