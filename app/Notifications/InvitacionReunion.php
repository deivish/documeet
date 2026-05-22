<?php

namespace App\Notifications;

use App\Models\Reunion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class InvitacionReunion extends Notification implements ShouldQueue
{
    use Queueable;

    public $reunion;

    public function __construct(Reunion $reunion)
    {
        $this->reunion = $reunion;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('📅 Nueva invitación a reunión: ' . $this->reunion->titulo)
            ->greeting('Hola ' . $notifiable->name)
            ->line('Has sido invitado a participar en la siguiente reunión:')
            ->line('**' . $this->reunion->titulo . '**')
            ->line('📝 Descripción: ' . ($this->reunion->descripcion ?: 'Sin descripción'))
            ->line('📅 Fecha: ' . $this->reunion->fecha_hora->format('d/m/Y'))
            ->line('⏰ Hora: ' . $this->reunion->fecha_hora->format('H:i'))
            ->line('👤 Organizado por: ' . $this->reunion->user->name)
            ->action('Ver detalles y aceptar', url('/reuniones/' . $this->reunion->id))
            ->line('Recibirás un recordatorio 24 horas antes de la reunión.')
            ->salutation('Saludos, Equipo DocuMeet');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'titulo' => $this->reunion->titulo,
            'fecha_hora' => $this->reunion->fecha_hora->toIso8601String(),
            'reunion_id' => $this->reunion->id,
            'organizador' => $this->reunion->user->name,
            'tipo' => 'invitacion',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'titulo' => $this->reunion->titulo,
            'fecha_hora' => $this->reunion->fecha_hora->toIso8601String(),
            'reunion_id' => $this->reunion->id,
            'organizador' => $this->reunion->user->name,
            'mensaje' => "Te han invitado a la reunión: {$this->reunion->titulo}",
        ]);
    }
}