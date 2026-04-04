<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;

class SalePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->esAdmin()
            || $user->can('sales.view')
            || $user->can('sales.create');
    }

    /**
     * Determine whether the user can view the model.
     * Admin ve todas; ejecutivo solo las que él registró (created_by).
     *
     * Importante: comprobar esAdmin() antes que can('sales.view'), porque si el rol admin
     * no tiene sincronizado ese permiso en Spatie, el admin quedaba bloqueado (403 en PDF, etc.).
     */
    public function view(User $user, Sale $sale): bool
    {
        if ($user->esAdmin()) {
            return true;
        }

        if (! $user->can('sales.view') && ! $user->can('sales.create')) {
            return false;
        }

        return (int) $sale->created_by === (int) $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->esAdmin() || $user->can('sales.create');
    }

    /**
     * Determine whether the user can update the model.
     * Admin edita todas; usuario solo las suyas (created_by).
     */
    public function update(User $user, Sale $sale): bool
    {
        if ($user->esAdmin()) {
            return true;
        }

        if (! $user->can('sales.edit')) {
            return false;
        }

        return (int) $sale->created_by === (int) $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     * Admin elimina todas; usuario con permiso solo las suyas (created_by).
     */
    public function delete(User $user, Sale $sale): bool
    {
        if ($user->esAdmin()) {
            return true;
        }

        if (! $user->can('sales.delete')) {
            return false;
        }

        return (int) $sale->created_by === (int) $user->id;
    }
}
