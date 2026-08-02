<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view beneficiaries', 'create beneficiaries', 'edit beneficiaries', 'delete beneficiaries',
            'view migrants', 'create migrants', 'edit migrants', 'delete migrants',
            'view returnees', 'create returnees', 'edit returnees', 'delete returnees',
            'view branches', 'create branches', 'edit branches', 'delete branches',
            'view staff', 'create staff', 'edit staff', 'delete staff',
            'view reports', 'export reports',
            'manage users', 'manage settings', 'view audit logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([
            'view beneficiaries', 'create beneficiaries', 'edit beneficiaries',
            'view migrants', 'create migrants', 'edit migrants',
            'view returnees', 'create returnees', 'edit returnees',
            'view branches',
            'view staff',
            'view reports', 'export reports',
        ]);

        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->syncPermissions([
            'view beneficiaries', 'create beneficiaries', 'edit beneficiaries',
            'view migrants', 'create migrants', 'edit migrants',
            'view returnees', 'create returnees', 'edit returnees',
            'view reports',
        ]);

        $fieldOfficer = Role::firstOrCreate(['name' => 'field-officer']);
        $fieldOfficer->syncPermissions([
            'view beneficiaries', 'create beneficiaries', 'edit beneficiaries',
            'view migrants', 'create migrants',
            'view returnees', 'create returnees',
        ]);

        $viewer = Role::firstOrCreate(['name' => 'viewer']);
        $viewer->syncPermissions([
            'view beneficiaries', 'view migrants', 'view returnees',
            'view reports',
        ]);

        $adminUser = User::where('email', 'admin@bracmis.org')->first();
        if ($adminUser) {
            $adminUser->assignRole('super-admin');
        }
    }
}
