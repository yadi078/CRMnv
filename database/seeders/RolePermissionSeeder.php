<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Seeder para Roles y Permisos
 * 
 * Crea los roles: Admin y Usuario
 * Define los permisos según las funcionalidades del sistema
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Resetear caché de roles y permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            // Empresas
            'companies.view',
            'companies.create',
            'companies.edit',
            'companies.delete',
            'companies.approve',
            'companies.export',
            'companies.import',
            
            // Contactos
            'contacts.view',
            'contacts.create',
            'contacts.edit',
            'contacts.delete',
            'contacts.export',
            'contacts.generate-pdf',
            
            // Seguimientos
            'follow-ups.view',
            'follow-ups.create',
            'follow-ups.edit',
            'follow-ups.delete',
            
            // Usuarios
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.approve',
            
            // Dashboard
            'dashboard.view',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear rol Admin con todos los permisos
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Crear rol Usuario con permisos limitados
        $userRole = Role::create(['name' => 'usuario']);
        $userRole->givePermissionTo([
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
        ]);
    }
}
