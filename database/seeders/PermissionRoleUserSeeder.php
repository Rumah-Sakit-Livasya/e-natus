<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionRoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat permissions
        $permissions = [
            // User Management
            'view users',
            'view roles',
            'view permissions',

            // Master Data
            'view asets',
            'view categories',
            'view clients',
            'view regions',
            'view templates',
            'view landers',

            // Project Management
            'view projects',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Buat role owner biasa dan berikan permission terbatas
        $ownerRole = Role::firstOrCreate(['name' => 'owner']);
        $ownerRole->syncPermissions(['view users', 'view roles']);

        // Buat role super-admin dan berikan semua permission
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdminRole->syncPermissions(Permission::all());

        // Buat role admin biasa dan berikan permission terbatas
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(['view users', 'view roles']);

        // Buat user super admin contoh
        $superAdminUser = User::firstOrCreate(
            ['email' => 'dimas@livasya.com'],
            [
                'name' => 'Dimas Candra Pebriyanto',
                'password' => bcrypt('dimas123'),
                'is_super_admin' => true,
            ]
        );
        $superAdminUser->assignRole($superAdminRole);

        // Buat user admin biasa contoh
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@livasya.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('admin123'),
                'is_super_admin' => false,
            ]
        );
        $adminUser->assignRole($adminRole);
    }
}
