<?php

namespace App\Policies;

use App\Models\FollowUp;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FollowUpPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('follow-ups.view');
    }

    /**
     * Determine whether the user can view the model.
     * Admin ve todos; usuario solo los que creó o tiene asignados.
     */
    public function view(User $user, FollowUp $followUp): bool
    {
        if (! $user->can('follow-ups.view')) {
            return false;
        }
        return $user->esAdmin()
            || $followUp->created_by === $user->id
            || $followUp->asignado_a === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('follow-ups.create');
    }

    /**
     * Determine whether the user can update the model.
     * Admin edita todos; usuario solo los que creó o tiene asignados.
     */
    public function update(User $user, FollowUp $followUp): bool
    {
        if (! $user->can('follow-ups.edit')) {
            return false;
        }
        return $user->esAdmin()
            || $followUp->created_by === $user->id
            || $followUp->asignado_a === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     * Admin elimina cualquiera; usuario solo los que creó o tiene asignados.
     */
    public function delete(User $user, FollowUp $followUp): bool
    {
        if (! $user->can('follow-ups.delete')) {
            return false;
        }
        return $user->esAdmin()
            || $followUp->created_by === $user->id
            || $followUp->asignado_a === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FollowUp $followUp): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FollowUp $followUp): bool
    {
        return false;
    }
}
