<?php

namespace App\Notifications;

use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;

class ContactBirthdayNotification extends Notification
{
    public function __construct(
        public Contact $contact,
        public ?Carbon $fechaReferencia = null
    ) {
        $this->fechaReferencia = ($fechaReferencia ?? now())->timezone(config('app.timezone'))->startOfDay();
    }

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
        $nacimiento = $this->contact->fecha_cumpleanos;
        $fechaTexto = $nacimiento ? $nacimiento->format('d/m') : '';
        $edad = $nacimiento ? $nacimiento->diffInYears($this->fechaReferencia) : null;

        $partes = ['Hoy cumple años', $nombre];
        if ($empresa !== '') {
            $partes[] = '('.$empresa.')';
        }
        if ($fechaTexto !== '') {
            $partes[] = '— '.$fechaTexto;
        }
        if ($edad !== null) {
            $partes[] = '· '.$edad.' años';
        }

        $mensaje = implode(' ', $partes);

        return [
            'titulo' => 'Cumpleaños de contacto',
            'mensaje' => $mensaje,
            'tipo' => 'cumpleanos',
            'type' => 'birthday',
            'message' => $mensaje,
            'contact_id' => $this->contact->id,
            'contact_name' => $nombre,
            'company_name' => $empresa,
            'fecha_cumpleanos' => $nacimiento?->format('d/m/Y'),
            'edad' => $edad,
            'entrar_url' => route('contacts.show', $this->contact),
        ];
    }
}
