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
        Schema::table('reunion_user', function (Blueprint $table) {
            $table->boolean('asistio')->default(false);
            $table->timestamp('hora_entrada')->nullable();
            $table->timestamp('hora_salida')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reunion_user', function (Blueprint $table) {
            //
        });
    }
};
