<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;

class ContactPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('contacts.view');
    }

    /**
     * Determine whether the user can view the model.
     * Admin ve todos; ejecutivo: asignados o creados por él.
     */
    public function view(User $user, Contact $contact): bool
    {
        if (! $user->can('contacts.view')) {
            return false;
        }
        if ($user->esAdmin()) {
            return true;
        }

        return (int) $contact->created_by === (int) $user->id
            || (int) $contact->assigned_user_id === (int) $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('contacts.create');
    }

    /**
     * Determine whether the user can update the model.
     * Admin edita todos; ejecutivo: creador o ejecutivo asignado.
     */
    public function update(User $user, Contact $contact): bool
    {
        if (! $user->can('contacts.edit')) {
            return false;
        }
        if ($user->esAdmin()) {
            return true;
        }

        return (int) $contact->created_by === (int) $user->id
            || (int) $contact->assigned_user_id === (int) $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     * Solo administradores pueden borrar sin pasar por aprobación.
     */
    public function delete(User $user, Contact $contact): bool
    {
        if (! $user->can('contacts.delete')) {
            return false;
        }

        return $user->esAdmin();
    }

    /**
     * Usuario (no admin) que creó el contacto: solicita eliminación para que un admin apruebe.
     */
    public function requestDeletion(User $user, Contact $contact): bool
    {
        if ($user->esAdmin()) {
            return false;
        }
        if (! $user->can('contacts.edit')) {
            return false;
        }
        if ($contact->approval_status !== 'aprobado' || $contact->deletion_pending) {
            return false;
        }

        return (int) $contact->created_by === (int) $user->id;
    }

    /**
     * Determine whether the user can generate PDF de ficha de inscripción.
     * Requiere contacts.generate-pdf; alcance igual que view (ejecutivo: su contacto).
     */
    public function generatePdf(User $user, Contact $contact): bool
    {
        if (! $user->can('contacts.generate-pdf')) {
            return false;
        }
        if ($user->esAdmin()) {
            return true;
        }

        return (int) $contact->created_by === (int) $user->id
            || (int) $contact->assigned_user_id === (int) $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Contact $contact): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Contact $contact): bool
    {
        return false;
    }
}
