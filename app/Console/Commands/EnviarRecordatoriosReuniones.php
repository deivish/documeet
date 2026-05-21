<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reunion;
use App\Notifications\RecordatorioReunion;
use Carbon\Carbon;

class EnviarRecordatoriosReuniones extends Command
{
    protected $signature = 'reuniones:recordatorios';
    protected $description = 'Enviar recordatorios de reuniones 24 horas antes';

    public function handle()
    {
        $this->info('Buscando reuniones para enviar recordatorios...');

        // Buscar reuniones que ocurrirán en las próximas 24 horas (+/- 15 minutos)
        $reuniones = Reunion::whereBetween('fecha_hora', [
            now()->addHours(23)->addMinutes(45),
            now()->addHours(24)->addMinutes(15)
        ])->get();

        $contadorEnviados = 0;

        foreach ($reuniones as $reunion) {
            // Obtener todos los invitados
            $invitados = $reunion->invitados;

            foreach ($invitados as $invitado) {
                // Enviar notificación
                $invitado->notify(new RecordatorioReunion($reunion));
                $contadorEnviados++;
            }

            $this->info("Recordatorios enviados para: {$reunion->titulo}");
        }

        $this->info("✅ Total de recordatorios enviados: {$contadorEnviados}");

        return Command::SUCCESS;
    }
}