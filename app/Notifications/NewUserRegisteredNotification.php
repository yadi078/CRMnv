<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;

class NewUserRegisteredNotification extends Notification
{
    public function __construct(
        public User $registeredUser
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Datos para la tabla notifications (canal database).
     * Estructura: titulo, mensaje, tipo, para compatibilidad con la vista tipo Gmail.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'titulo' => 'Nuevo usuario registrado',
            'mensaje' => 'Se registró el usuario ' . $this->registeredUser->name,
            'tipo' => 'registro',
            'type' => 'new_user',
            'user_id' => $this->registeredUser->id,
            'user_name' => $this->registeredUser->name,
            'user_email' => $this->registeredUser->email,
        ];
    }
}
