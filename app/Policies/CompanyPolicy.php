<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

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
     * Admin ve todas (con permisos). Ejecutivo: empresa suya o con algún contacto asignado/creado por él.
     */
    public function view(User $user, Company $company): bool
    {
        if (! $user->can('companies.view')) {
            return false;
        }

        if ($user->esAdmin()) {
            return true;
        }

        return $company->isAccessibleByExecutive($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('companies.create');
    }

    /**
     * Admin edita todas. Ejecutivo solo las que registró.
     */
    public function update(User $user, Company $company): bool
    {
        if (! $user->can('companies.edit')) {
            return false;
        }

        if ($user->esAdmin()) {
            return true;
        }

        return (int) $company->created_by === (int) $user->id;
    }

    /**
     * Borrado inmediato solo para administradores con companies.delete.
     * Los ejecutivos deben usar requestDeletion (aprobación de admin).
     */
    public function delete(User $user, Company $company): bool
    {
        if (! $user->esAdmin()) {
            return false;
        }

        return $user->can('companies.delete');
    }

    /**
     * Solicitud de baja (ejecutivo dueño de empresa aprobada).
     */
    public function requestDeletion(User $user, Company $company): bool
    {
        if ($user->esAdmin()) {
            return false;
        }
        if (! $user->can('companies.edit')) {
            return false;
        }
        if ($company->deletion_pending) {
            return false;
        }
        if ($company->approval_status !== 'aprobado') {
            return false;
        }

        return (int) $company->created_by === (int) $user->id;
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
