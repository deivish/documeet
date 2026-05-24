<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actas', function (Blueprint $table) {
            $table->longText('resumen')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('actas', function (Blueprint $table) {
            $table->string('resumen')->nullable()->change();
        });
    }
};