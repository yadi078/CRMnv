<?php

namespace App\Notifications;

use App\Models\Contact;
use Illuminate\Notifications\Notification;

class ContactBirthdayNotification extends Notification
{
    public function __construct(
        public Contact $contact
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $nombre = $this->contact->nombre_completo;
        $empresa = $this->contact->company?->nombre_comercial ?? '';

        return [
            'titulo' => 'Cumpleaños',
            'mensaje' => 'Hoy es cumpleaños de ' . $nombre . ($empresa ? ' (' . $empresa . ')' : ''),
            'tipo' => 'contacto',
            'type' => 'birthday',
            'message' => 'Hoy es cumpleaños de ' . $nombre,
            'contact_id' => $this->contact->id,
            'contact_name' => $nombre,
            'company_name' => $empresa,
            'entrar_url' => route('contacts.show', $this->contact),
        ];
    }
}
