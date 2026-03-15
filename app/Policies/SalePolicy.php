<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Auth\Access\Response;

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
     * Admin ve todas; usuario solo las suyas (created_by).
     */
    public function view(User $user, Sale $sale): bool
    {
        if (! $user->can('sales.view')) {
            return false;
        }
        return $user->esAdmin() || $sale->created_by === $user->id;
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
