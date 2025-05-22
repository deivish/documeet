<?php

namespace App\Notifications;

use App\Models\Reunion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class InvitacionReunion extends Notification
{
    use Queueable;

    public $reunion;
    /**
     * Create a new notification instance.
     */
    public function __construct(Reunion $reunion)
    {
        $this->reunion = $reunion;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nueva invitación a una reunión')
            ->greeting('Hola ' . $notifiable->name)
            ->line('Has sido invitado a la reunión: ' . $this->reunion->titulo)
            ->line('Fecha y hora: ' . $this->reunion->fecha_hora)
            ->action('Ver detalles', url('/reuniones/' . $this->reunion->id))
            ->line('Gracias por usar nuestra aplicación.');

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'titulo' => $this->reunion->titulo,
            'fecha_hora' => $this->reunion->fecha_hora,
            'reunion_id' => $this->reunion->id,
        ];
    }

    public function toBroadcast($notifiable)
    {
    return new BroadcastMessage([
        'titulo' => $this->reunion->titulo,
        'fecha_hora' => $this->reunion->fecha_hora,
        'reunion_id' => $this->reunion->id,
    ]);
}
}
