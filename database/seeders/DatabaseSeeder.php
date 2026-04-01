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

        // Administradores (config/admin.php y .env: ADMIN_EMAIL, ADMIN_EMAIL_2, etc.)
        $this->call(AdminUserSeeder::class);

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
