<?php

namespace App\Notifications;

use App\Models\Contact;
use Illuminate\Notifications\Notification;

class NewContactAddedNotification extends Notification
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

    /**
     * Datos para la tabla notifications (canal database).
     * Incluye titulo, mensaje, tipo para la vista tipo Gmail.
     */
    public function toArray(object $notifiable): array
    {
        $companyName = $this->contact->company?->nombre_comercial ?? 'empresa';
        $msg = 'Nuevo contacto: ' . $this->contact->nombre_completo . ' (' . $this->contact->email . ') en ' . $companyName;
        return [
            'titulo' => 'Nuevo cliente agregado',
            'mensaje' => 'Se agregó el cliente ' . $companyName,
            'tipo' => 'contacto',
            'type' => 'new_client',
            'message' => $msg,
            'contact_id' => $this->contact->id,
            'contact_name' => $this->contact->nombre_completo,
            'company_name' => $companyName,
            'entrar_url' => route('contacts.show', $this->contact),
        ];
    }
}
