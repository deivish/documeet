<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reuniones', function (Blueprint $table) {
            $table->string('daily_url')->nullable()->after('descripcion');
            $table->string('daily_room_name')->nullable()->after('daily_url');
            $table->timestamp('daily_expires_at')->nullable()->after('daily_room_name');
        });
    }

    public function down()
    {
        Schema::table('reuniones', function (Blueprint $table) {
            $table->dropColumn(['daily_url', 'daily_room_name', 'daily_expires_at']);
        });
    }
};