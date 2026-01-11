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
        Schema::table('admins', function (Blueprint $table) {
            // Add parent_id only if it does not exist
            if (!Schema::hasColumn('admins', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('id');

                $table->foreign('parent_id')
                      ->references('id')
                      ->on('admins')
                      ->onDelete('cascade');
            }
        });

        // Update type column comment
        DB::statement("
            ALTER TABLE `admins`
            MODIFY COLUMN `type` TINYINT NOT NULL DEFAULT 4
            COMMENT '0 = Super Admin, 1 = Admin, 2 = Public Vendor, 3 = Private Vendor, 4 = User'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
        });

        // Revert type column (remove comment)
        DB::statement("
            ALTER TABLE `admins`
            MODIFY COLUMN `type` TINYINT NOT NULL DEFAULT 4
        ");
    }
};
