<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::rename('feed_mills', 'feed_suppliers');
    }

    public function down()
    {
        Schema::rename('feed_suppliers', 'feed_mills');
    }
};
