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
            ->subject('Nueva invitación a una reunión')
            ->greeting('Hola ' . $notifiable->name)
            ->line('Has sido invitado a la reunión: ' . $this->reunion->titulo)
            ->line('Fecha y hora: ' . $this->reunion->fecha_hora->format('d/m/Y H:i'))
            ->action('Ver detalles', url('/reuniones/' . $this->reunion->id))
            ->line('Gracias por usar DocuMeet.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'titulo' => $this->reunion->titulo,
            'fecha_hora' => $this->reunion->fecha_hora->toIso8601String(),
            'reunion_id' => $this->reunion->id,
            'organizador' => $this->reunion->user->name,
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
