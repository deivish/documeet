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
            Schema::create('actas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reunion_id')->constrained('reunions')->onDelete('cascade');
            $table->string('numero')->nullable();
            $table->enum('estado', ['borrador','final'])->default('borrador');
            $table->string('resumen')->nullable();
            $table->longText('desarrollo')->nullable();
            $table->foreignId('redactada_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('aprobada_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('aprobada_at')->nullable();
            $table->string('archivo_pdf')->nullable();
            $table->string('archivo_docx')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actas');
    }
};
