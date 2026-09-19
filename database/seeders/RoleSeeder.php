<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'editor']);

        // Optional: Create permissions
        $permissions = [
            'manage tables',
            'manage columns',
            'manage rows',
            'manage languages',
            'manage settings',
            'import data',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign all permissions to admin
        $adminRole = Role::findByName('admin');
        $adminRole->givePermissionTo(Permission::all());

        // Assign limited permissions to editor
        $editorRole = Role::findByName('editor');
        $editorRole->givePermissionTo([
            'manage tables',
            'manage columns',
            'manage rows',
            'import data',
        ]);
    }
}
