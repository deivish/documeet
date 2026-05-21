<?php

namespace App\Notifications;

use App\Models\Reunion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class CambioHoraReunion extends Notification implements ShouldQueue
{
    use Queueable;

    public $reunion;
    public $fechaHoraAnterior;

    public function __construct(Reunion $reunion, $fechaHoraAnterior)
    {
        $this->reunion = $reunion;
        $this->fechaHoraAnterior = $fechaHoraAnterior;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $fechaAnterior = Carbon::parse($this->fechaHoraAnterior);
        
        return (new MailMessage)
            ->subject('⚠️ Cambio de hora en reunión: ' . $this->reunion->titulo)
            ->greeting('Hola ' . $notifiable->name)
            ->line('Se ha modificado la fecha/hora de una reunión:')
            ->line('**' . $this->reunion->titulo . '**')
            ->line('---')
            ->line('❌ **Hora anterior:**')
            ->line('📅 ' . $fechaAnterior->format('d/m/Y') . ' ⏰ ' . $fechaAnterior->format('H:i'))
            ->line('---')
            ->line('✅ **Nueva hora:**')
            ->line('📅 ' . $this->reunion->fecha_hora->format('d/m/Y') . ' ⏰ ' . $this->reunion->fecha_hora->format('H:i'))
            ->line('---')
            ->line('👤 Modificado por: ' . $this->reunion->organizador->name)
            ->action('Ver detalles actualizados', url('/reuniones/' . $this->reunion->id))
            ->line('Por favor, ajusta tu calendario con la nueva hora.')
            ->salutation('Saludos, Equipo DocuMeet');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'titulo' => 'Cambio de hora: ' . $this->reunion->titulo,
            'fecha_hora' => $this->reunion->fecha_hora->toIso8601String(),
            'fecha_hora_anterior' => Carbon::parse($this->fechaHoraAnterior)->toIso8601String(),
            'reunion_id' => $this->reunion->id,
            'tipo' => 'cambio_hora',
        ];
    }
}