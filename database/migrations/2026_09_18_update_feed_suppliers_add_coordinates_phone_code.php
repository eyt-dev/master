<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('feed_suppliers', function (Blueprint $table) {
            $table->dropColumn('address');
            $table->string('phone_code')->after('contact_person');
            $table->decimal('latitude', 10, 8)->nullable()->after('location');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    public function down()
    {
        Schema::table('feed_suppliers', function (Blueprint $table) {
            $table->text('address')->nullable();
            $table->dropColumn(['phone_code', 'latitude', 'longitude']);
        });
    }
};
