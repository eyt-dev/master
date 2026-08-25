<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Normalize phone_code values - ensure all have + prefix
        $admins = DB::table('admins')
            ->whereNotNull('phone_code')
            ->where('phone_code', '!=', '')
            ->get();

        foreach ($admins as $admin) {
            $phoneCode = $admin->phone_code;

            // Add + prefix if not present
            if (!str_starts_with($phoneCode, '+')) {
                DB::table('admins')
                    ->where('id', $admin->id)
                    ->update(['phone_code' => '+' . $phoneCode]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove + prefix to revert
        $admins = DB::table('admins')
            ->whereNotNull('phone_code')
            ->where('phone_code', '!=', '')
            ->get();

        foreach ($admins as $admin) {
            $phoneCode = $admin->phone_code;

            // Remove + prefix if present
            if (str_starts_with($phoneCode, '+')) {
                DB::table('admins')
                    ->where('id', $admin->id)
                    ->update(['phone_code' => ltrim($phoneCode, '+')]);
            }
        }
    }
};
