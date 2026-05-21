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
        Schema::create('compromiso_seguimiento', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('compromiso_id');
            $table->unsignedBigInteger('reunion_id');
            $table->enum('estado', ['revisado', 'pendiente', 'escalado'])->default('pendiente');
            $table->text('nota')->nullable();
            $table->timestamps();

            $table->foreign('compromiso_id')->references('id')->on('compromisos')->cascadeOnDelete();
            $table->foreign('reunion_id')->references('id')->on('reunions')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compromiso_seguimiento');
    }
};
