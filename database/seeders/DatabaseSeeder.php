<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar seeder de roles y permisos
        $this->call(RolePermissionSeeder::class);

        // Crear usuario administrador (email/contraseña: config/admin.php y .env)
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
            $admin->assignRole('admin');
        }

        // Crear usuario normal (pendiente de aprobación)
        $usuario = User::create([
            'name' => 'Usuario Normal',
            'email' => 'usuario@cceconsultoria.com',
            'password' => Hash::make('password'),
            'approval_status' => 'pendiente',
        ]);
        $usuario->assignRole('usuario');
    }
}
