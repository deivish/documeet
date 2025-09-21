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
        Schema::create('compromisos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('reunion_id')->constrained()->onDelete('cascade');
        $table->string('descripcion');   // Compromiso
        $table->string('responsable');
        $table->date('fecha');
        $table->string('resultado')->nullable(); // Resultado esperado
        $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compromisos');
    }
};
