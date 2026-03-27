<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Asegura que el usuario administrador exista y tenga el rol admin.
 * Se puede ejecutar solo: php artisan db:seed --class=AdminUserSeeder
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::firstOrCreate(['name' => 'admin'], ['guard_name' => 'web']);

        $adminEmail = config('admin.email');
        $adminPassword = config('admin.password');

        $admin = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Administrador',
                'password' => Hash::make($adminPassword),
                'approval_status' => 'aprobado',
                'approved_at' => now(),
            ]
        );

        $admin->update(['password' => Hash::make($adminPassword)]);

        if (! $admin->hasRole('admin')) {
            $admin->assignRole($role);
        }
    }
}
