<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Company;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;

trait ResolvesExecutiveAssignment
{
    protected function defaultAssignedUserId(?User $auth = null): ?int
    {
        $auth = $auth ?? auth()->user();
        if (! $auth) {
            return null;
        }
        if ($auth->hasRole('usuario')) {
            return (int) $auth->id;
        }
        $first = User::ejecutivosAsignables()->first();

        return $first ? (int) $first->id : null;
    }

    protected function assertAssignableExecutive(int $userId): User
    {
        $allowed = User::ejecutivosAsignables()->pluck('id')->map(fn ($id) => (int) $id)->all();
        if (! in_array($userId, $allowed, true)) {
            abort(422, 'El ejecutivo seleccionado no es válido.');
        }

        return User::query()->findOrFail($userId);
    }

    /**
     * @return array{assigned_user_id: int|null, ejecutivo_asignado: string|null}
     */
    protected function resolveCompanyExecutiveForSave(Request $request, User $auth, bool $isUpdate, ?Company $company = null): array
    {
        if (! $auth->esAdmin()) {
            if ($isUpdate && $company) {
                return [
                    'assigned_user_id' => $company->assigned_user_id,
                    'ejecutivo_asignado' => $company->ejecutivo_asignado,
                ];
            }

            return [
                'assigned_user_id' => $auth->id,
                'ejecutivo_asignado' => $auth->name,
            ];
        }

        if ($isUpdate && $company && ! $request->has('assigned_user_id')) {
            return [
                'assigned_user_id' => $company->assigned_user_id,
                'ejecutivo_asignado' => $company->ejecutivo_asignado,
            ];
        }

        $raw = $request->input('assigned_user_id');
        $requestedId = $raw !== null && $raw !== '' ? (int) $raw : null;

        if ($requestedId === null && ! $isUpdate) {
            $requestedId = $this->defaultAssignedUserId($auth);
        }

        if ($requestedId === null && $isUpdate && $company) {
            return [
                'assigned_user_id' => $company->assigned_user_id,
                'ejecutivo_asignado' => $company->ejecutivo_asignado,
            ];
        }

        if ($requestedId === null) {
            return ['assigned_user_id' => null, 'ejecutivo_asignado' => null];
        }

        $exec = $this->assertAssignableExecutive($requestedId);

        return [
            'assigned_user_id' => (int) $exec->id,
            'ejecutivo_asignado' => $exec->name,
        ];
    }

    protected function resolveContactExecutiveForSave(Request $request, User $auth, bool $isUpdate, ?Contact $contact = null): ?int
    {
        if (! $auth->esAdmin()) {
            if ($isUpdate && $contact) {
                return $contact->assigned_user_id;
            }

            return (int) $auth->id;
        }

        if ($isUpdate && $contact && ! $request->has('assigned_user_id')) {
            return $contact->assigned_user_id;
        }

        $raw = $request->input('assigned_user_id');
        $requestedId = $raw !== null && $raw !== '' ? (int) $raw : null;

        if ($requestedId === null && ! $isUpdate) {
            $requestedId = $this->defaultAssignedUserId($auth);
        }

        if ($requestedId === null && $isUpdate && $contact) {
            return $contact->assigned_user_id;
        }

        if ($requestedId === null) {
            return null;
        }

        return (int) $this->assertAssignableExecutive($requestedId)->id;
    }

    /**
     * @return array{executiveUsers: \Illuminate\Support\Collection, isAdmin: bool, selectedAssignedUserId: int|null, readonlyExecutiveName: string}
     */
    protected function companyExecutiveFormContext(?Company $company = null): array
    {
        $user = auth()->user();
        $executiveUsers = User::ejecutivosAsignables();
        $isAdmin = $user->esAdmin();
        $defaultId = $this->defaultAssignedUserId($user);

        $resolvedFromLegacy = null;
        if ($company && ! $company->assigned_user_id && $company->ejecutivo_asignado) {
            $resolvedFromLegacy = $executiveUsers->firstWhere('name', $company->ejecutivo_asignado)?->id;
        }

        $selectedAssignedUserId = old(
            'assigned_user_id',
            $company?->assigned_user_id ?? $resolvedFromLegacy ?? $defaultId
        );
        if ($selectedAssignedUserId !== null) {
            $selectedAssignedUserId = (int) $selectedAssignedUserId;
        }

        $readonlyExecutiveName = $company
            ? ($company->assignedExecutive?->name ?? $company->ejecutivo_asignado ?? $user->name)
            : $user->name;

        return [
            'executiveUsers' => $executiveUsers,
            'isAdmin' => $isAdmin,
            'selectedAssignedUserId' => $selectedAssignedUserId,
            'readonlyExecutiveName' => (string) $readonlyExecutiveName,
        ];
    }

    /**
     * @return array{executiveUsers: \Illuminate\Support\Collection, isAdmin: bool, selectedAssignedUserId: int|null, readonlyExecutiveName: string}
     */
    protected function contactExecutiveFormContext(?Contact $contact = null): array
    {
        $user = auth()->user();
        $executiveUsers = User::ejecutivosAsignables();
        $isAdmin = $user->esAdmin();
        $defaultId = $this->defaultAssignedUserId($user);

        $selectedAssignedUserId = old(
            'assigned_user_id',
            $contact?->assigned_user_id ?? $defaultId
        );
        if ($selectedAssignedUserId !== null) {
            $selectedAssignedUserId = (int) $selectedAssignedUserId;
        }

        $readonlyExecutiveName = $contact
            ? ($contact->assignedExecutive?->name ?? $user->name)
            : $user->name;

        return [
            'executiveUsers' => $executiveUsers,
            'isAdmin' => $isAdmin,
            'selectedAssignedUserId' => $selectedAssignedUserId,
            'readonlyExecutiveName' => (string) $readonlyExecutiveName,
        ];
    }
}
