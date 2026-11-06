<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view beneficiaries', 'create beneficiaries', 'edit beneficiaries', 'delete beneficiaries',
            'view migrants', 'create migrants', 'edit migrants', 'delete migrants',
            'view returnees', 'create returnees', 'edit returnees', 'delete returnees',
            'view branches', 'create branches', 'edit branches', 'delete branches',
            'view awareness programs', 'create awareness programs', 'edit awareness programs', 'delete awareness programs',
            'view immediate support', 'create immediate support', 'edit immediate support', 'delete immediate support',
            'view courses', 'create courses', 'edit courses', 'delete courses',
            'view certificates', 'create certificates', 'edit certificates', 'delete certificates',
            'view staff', 'create staff', 'edit staff', 'delete staff',
            'view designations', 'create designations', 'edit designations', 'delete designations',
            'view staff incentives', 'create staff incentives', 'edit staff incentives', 'delete staff incentives',
            'view finances', 'create finances', 'edit finances', 'delete finances',
            'view reports', 'export reports',
            'manage users', 'manage roles', 'manage permissions', 'manage settings', 'view audit logs',
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
            'view awareness programs',
            'view immediate support', 'create immediate support', 'edit immediate support', 'delete immediate support',
            'view courses', 'create courses', 'edit courses', 'delete courses',
            'view certificates', 'create certificates', 'edit certificates', 'delete certificates',
            'view staff',
            'view designations',
            'view staff incentives',
            'view finances', 'create finances', 'edit finances',
            'view reports', 'export reports',
        ]);

        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->syncPermissions([
            'view beneficiaries', 'create beneficiaries', 'edit beneficiaries',
            'view migrants', 'create migrants', 'edit migrants',
            'view returnees', 'create returnees', 'edit returnees',
            'view immediate support', 'create immediate support', 'edit immediate support',
            'view courses', 'create courses', 'edit courses',
            'view certificates', 'create certificates',
            'view finances',
            'view reports',
        ]);

        $fieldOfficer = Role::firstOrCreate(['name' => 'field-officer']);
        $fieldOfficer->syncPermissions([
            'view beneficiaries', 'create beneficiaries', 'edit beneficiaries',
            'view migrants', 'create migrants',
            'view returnees', 'create returnees',
            'view immediate support', 'create immediate support',
            'view courses', 'view certificates',
        ]);

        $viewer = Role::firstOrCreate(['name' => 'viewer']);
        $viewer->syncPermissions([
            'view beneficiaries', 'view migrants', 'view returnees',
            'view awareness programs', 'view immediate support', 'view finances', 'view reports',
            'view courses', 'view certificates',
        ]);

        $adminUser = User::where('email', 'admin@bracmis.org')->first();
        if ($adminUser) {
            $adminUser->assignRole('super-admin');
        }
    }
}
