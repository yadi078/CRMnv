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
            'contacts.approve',
            'contacts.export',
            'contacts.generate-pdf',
            
            // Seguimientos
            'follow-ups.view',
            'follow-ups.create',
            'follow-ups.edit',
            'follow-ups.delete',

            // Historial de ventas
            'sales.view',
            'sales.create',
            'sales.edit',
            'sales.delete',
            
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
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear rol Admin con todos los permisos
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web']
        );
        $adminRole->syncPermissions(Permission::all());

        // Alias en español (misma capacidad que admin; usado en User::esAdmin())
        $adminEsRole = Role::firstOrCreate(
            ['name' => 'administrador', 'guard_name' => 'web']
        );
        $adminEsRole->syncPermissions(Permission::all());

        // Crear rol Usuario con permisos limitados
        $userRole = Role::firstOrCreate(
            ['name' => 'usuario', 'guard_name' => 'web']
        );
        $userRole->givePermissionTo([
            'companies.view',
            'companies.create',
            'companies.edit',
            'contacts.view',
            'contacts.create',
            'contacts.edit',
            'contacts.generate-pdf',
            'follow-ups.view',
            'follow-ups.create',
            'follow-ups.edit',
            'sales.view',
            'sales.create',
            'sales.edit',
            'sales.delete',
            'dashboard.view',
        ]);
    }
}
