<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/**
 * Notifica al usuario que solicitó eliminar una empresa o contacto del resultado (aprobada / denegada).
 */
class DeletionRequestResolvedNotification extends Notification
{
    public function __construct(
        public string $entity, // company | contact
        public string $outcome, // approved | denied
        public string $entityName,
        public ?string $adminNote = null,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $tipoEntidad = $this->entity === 'company' ? 'empresa' : 'contacto';
        $approved = $this->outcome === 'approved';

        $titulo = $approved
            ? 'Eliminación aprobada'
            : 'Eliminación no aprobada';

        if ($approved) {
            $mensaje = "Se aprobó la baja del {$tipoEntidad} «{$this->entityName}». Ya no aparecerá en tu listado activo.";
        } else {
            $mensaje = "No se aprobó la eliminación del {$tipoEntidad} «{$this->entityName}». Motivo indicado por el administrador: "
                .($this->adminNote ?: 'Sin detalle.');
        }

        return [
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'tipo' => 'eliminacion_solicitud',
            'entity' => $this->entity,
            'outcome' => $this->outcome,
            'entity_name' => $this->entityName,
            'nota_admin' => $this->adminNote,
        ];
    }
}
