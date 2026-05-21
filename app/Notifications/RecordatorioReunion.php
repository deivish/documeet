<?php

namespace App\Notifications;

use App\Models\Reunion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecordatorioReunion extends Notification implements ShouldQueue
{
    use Queueable;

    public $reunion;

    public function __construct(Reunion $reunion)
    {
        $this->reunion = $reunion;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $horasRestantes = now()->diffInHours($this->reunion->fecha_hora);
        
        return (new MailMessage)
            ->subject('🔔 Recordatorio: Reunión en ' . $horasRestantes . ' horas')
            ->greeting('Hola ' . $notifiable->name)
            ->line('Te recordamos que tienes una reunión programada:')
            ->line('**' . $this->reunion->titulo . '**')
            ->line('📅 Fecha: ' . $this->reunion->fecha_hora->format('d/m/Y'))
            ->line('⏰ Hora: ' . $this->reunion->fecha_hora->format('H:i'))
            ->line('👤 Organizado por: ' . $this->reunion->organizador->name)
            ->action('Ver detalles de la reunión', url('/reuniones/' . $this->reunion->id))
            ->line('¡Prepárate para la reunión!')
            ->salutation('Saludos, Equipo DocuMeet');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'titulo' => 'Recordatorio: ' . $this->reunion->titulo,
            'fecha_hora' => $this->reunion->fecha_hora->toIso8601String(),
            'reunion_id' => $this->reunion->id,
            'tipo' => 'recordatorio',
        ];
    }
}