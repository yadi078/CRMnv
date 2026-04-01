<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $permission = Permission::firstOrCreate(
            ['name' => 'contacts.approve', 'guard_name' => 'web']
        );

        foreach (['admin', 'administrador'] as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role && ! $role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
            }
        }
    }

    public function down(): void
    {
        $permission = Permission::where('name', 'contacts.approve')->where('guard_name', 'web')->first();
        if ($permission) {
            $permission->delete();
        }
    }
};
