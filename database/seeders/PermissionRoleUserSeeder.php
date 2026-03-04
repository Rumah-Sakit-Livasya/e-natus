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
            'view aset master',
            'view categories',
            'view aset categories',
            'view clients',
            'view regions',
            'view templates',
            'view aset templates',
            'view landers',
            'view aset landers',
            'view dokters',
            'view sdm',
            'view supplier',

            // Project Management
            'view projects',
            'approve_project_level_1',
            'approve_project_level_2',
            'view procurement',
            'view aset procurement',
            'view realisations',
            'view aset realisations',
            'view aset receipt',
            'view aset receipts',
            'view rab awal',
            'view rab closing',
            'view pengajuan dana',
            'approve pengajuan_dana',
            'view attendance project',
            'view request attendance project',
            'view participant project',
            'view mcu result',
            'view hasil mcu',
            'view laporan margin',
            'view laporan pemeriksaan',
            'view stock opname',
            'view bmhp stock opname',
            'view bmhp',
            'view bmhp master',
            'view bmhp purchases',
            'view bmhp stock status',
            'view bmhp office usage',
            'view bmhp remainders',
            'approve bmhp',
            'print invoice project',
            'rab manage',
            'view employees',
            'view notifications',
            'view vendor rentals',
            'view aset vendor rentals',
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
