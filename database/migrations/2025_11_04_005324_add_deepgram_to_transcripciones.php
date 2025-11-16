<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE transcripciones MODIFY COLUMN fuente ENUM('manual', 'stt', 'webspeech', 'deepgram') NOT NULL DEFAULT 'webspeech'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE transcripciones MODIFY COLUMN fuente ENUM('manual', 'stt', 'webspeech') NOT NULL DEFAULT 'webspeech'");
    }
};