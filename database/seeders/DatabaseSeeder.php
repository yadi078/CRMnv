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

        // Crear usuario administrador
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@cceconsultoria.com',
            'password' => Hash::make('password'),
            'approval_status' => 'aprobado',
            'approved_at' => now(),
        ]);
        $admin->assignRole('admin');

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
