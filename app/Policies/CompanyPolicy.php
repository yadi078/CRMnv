<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CompanyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('companies.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Company $company): bool
    {
        // Usuarios normales solo pueden ver empresas aprobadas
        if (!$user->can('companies.approve') && $company->approval_status === 'pendiente') {
            return false;
        }
        return $user->can('companies.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('companies.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Company $company): bool
    {
        return $user->can('companies.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Company $company): bool
    {
        return $user->can('companies.delete');
    }

    /**
     * Usuario sin permiso de borrado directo puede pedir que un admin autorice la eliminación.
     */
    public function requestDeletion(User $user, Company $company): bool
    {
        if (! $user->can('companies.edit') || $user->can('companies.delete')) {
            return false;
        }
        if ($company->approval_status !== 'aprobado' || $company->deletion_pending) {
            return false;
        }
        if ((int) $company->created_by !== (int) $user->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Company $company): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Company $company): bool
    {
        return false;
    }
}
