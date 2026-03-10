<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $entrarUrl
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Correo con el enlace para entrar directamente al panel (un clic).
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu cuenta ha sido aprobada - CE CRM')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Tu cuenta ha sido aprobada por un administrador.')
            ->line('Haz clic en el botón de abajo para entrar directamente a tu panel (no necesitas escribir tu contraseña).')
            ->action('Entrar a mi panel', $this->entrarUrl)
            ->line('Este enlace es válido durante 2 días. Si no usaste este correo, puedes ignorarlo.');
    }

    /**
     * Datos para la tabla notifications (se verá cuando ya haya entrado).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'titulo' => 'Cuenta aprobada',
            'mensaje' => 'Tu cuenta ha sido aprobada. Haz clic en el enlace para entrar a tu panel.',
            'tipo' => 'aprobacion',
            'type' => 'user_approved',
            'entrar_url' => $this->entrarUrl,
        ];
    }
}
