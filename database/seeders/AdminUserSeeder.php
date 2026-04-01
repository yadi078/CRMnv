<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Asegura que los usuarios administradores existan y tengan el rol admin.
 * Se puede ejecutar solo: php artisan db:seed --class=AdminUserSeeder
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::firstOrCreate(['name' => 'admin'], ['guard_name' => 'web']);
        $permissions = Permission::query()->where('guard_name', 'web')->get();
        if ($permissions->isNotEmpty()) {
            // Evita admins "sin permisos" cuando solo se ejecuta este seeder.
            $role->syncPermissions($permissions);
        }

        foreach (config('admin.admins') as $adminConfig) {
            $email = $adminConfig['email'];
            $password = $adminConfig['password'];
            $name = $adminConfig['name'];

            $admin = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make($password),
                    'approval_status' => 'aprobado',
                    'approved_at' => now(),
                ]
            );

            $admin->update([
                'name' => $name,
                'password' => Hash::make($password),
            ]);

            if (! $admin->hasRole('admin')) {
                $admin->assignRole($role);
            }
        }
    }
}
