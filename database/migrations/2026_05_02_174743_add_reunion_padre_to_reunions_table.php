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
        Schema::table('reunions', function (Blueprint $table) {
            $table->unsignedBigInteger('reunion_padre_id')
              ->nullable()
              ->after('user_id');
            $table->foreign('reunion_padre_id')
              ->references('id')
              ->on('reunions')
              ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reunions', function (Blueprint $table) {
            $table->dropForeign(['reunion_padre_id']);
            $table->dropColumn('reunion_padre_id');
        });
    }
};
