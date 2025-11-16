<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Modificar el ENUM para agregar 'webspeech'
        DB::statement("ALTER TABLE transcripciones MODIFY COLUMN fuente ENUM('manual', 'stt', 'webspeech') NOT NULL DEFAULT 'webspeech'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE transcripciones MODIFY COLUMN fuente ENUM('manual', 'stt') NOT NULL DEFAULT 'stt'");
    }
};