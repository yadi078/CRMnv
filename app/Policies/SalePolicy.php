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
        return $user->can('sales.view');
    }

    /**
     * Determine whether the user can view the model.
     * Admin ve todas; ejecutivo: las que registró o cualquiera de una empresa a la que tiene acceso
     * (alineado con la ficha de empresa, donde se listan todas las ventas del cliente).
     */
    public function view(User $user, Sale $sale): bool
    {
        if (! $user->can('sales.view')) {
            return false;
        }
        if ($user->esAdmin()) {
            return true;
        }
        if ((int) $sale->created_by === (int) $user->id) {
            return true;
        }

        $sale->loadMissing('company');

        return $sale->company !== null && $sale->company->isAccessibleByExecutive($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('sales.create');
    }

    /**
     * Determine whether the user can update the model.
     * Admin edita todas; usuario solo las suyas (created_by).
     */
    public function update(User $user, Sale $sale): bool
    {
        if (! $user->can('sales.edit')) {
            return false;
        }
        return $user->esAdmin() || $sale->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     * Admin elimina todas; usuario con permiso solo las suyas (created_by).
     */
    public function delete(User $user, Sale $sale): bool
    {
        if (! $user->can('sales.delete')) {
            return false;
        }
        return $user->esAdmin() || $sale->created_by === $user->id;
    }
}
