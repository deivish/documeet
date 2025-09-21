<?php

namespace App\Notifications;

use App\Models\Acta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ActaGenerada extends Notification implements ShouldQueue
{
    use Queueable;

    public $acta;

    public function __construct(Acta $acta)
    {
        $this->acta = $acta;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // email + base de datos
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Se ha generado un acta')
            ->greeting('Hola ' . $notifiable->name)
            ->line('El acta de la reunión "' . $this->acta->reunion->titulo . '" ha sido generada.')
            ->action('Ver Acta', route('actas.show', $this->acta->id))
            ->line('Gracias por usar nuestra plataforma.');
    }

    public function toArray($notifiable)
    {
        return [
            'acta_id' => $this->acta->id,
            'reunion_id' => $this->acta->reunion->id,
            'titulo' => $this->acta->reunion->titulo,
        ];
    }
}
