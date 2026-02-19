<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpFoundation\Response;

/**
 * Asigna el rol "usuario" a usuarios autenticados que no tengan ningún rol.
 * Si el rol o los permisos no existen (ej. no se ejecutó el seeder), los crea.
 * Evita 403 en empresas, contactos, etc.
 */
class EnsureUserHasRole
{
    /** Permisos mínimos del rol usuario (igual que RolePermissionSeeder) */
    private const USUARIO_PERMISSIONS = [
        'companies.view',
        'companies.create',
        'companies.edit',
        'contacts.view',
        'contacts.create',
        'contacts.edit',
        'follow-ups.view',
        'follow-ups.create',
        'follow-ups.edit',
        'dashboard.view',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->roles()->count() > 0) {
            return $next($request);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::firstOrCreate(['name' => 'usuario']);

        // Asegurar que el rol tenga los permisos (crear permisos si no existen)
        $permissions = collect(self::USUARIO_PERMISSIONS)->map(function (string $name) {
            return Permission::firstOrCreate(['name' => $name]);
        });

        $role->syncPermissions($permissions);
        $user->assignRole($role);

        return $next($request);
    }
}
