<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BranchSeeder::class,
            AdminUserSeeder::class,
            RolePermissionSeeder::class,
            StaffSeeder::class,
            BeneficiarySeeder::class,
            MigrationSeeder::class,
            ReturneeSeeder::class,
            DesignationSeeder::class,
            SettingSeeder::class,
            AuditLogSeeder::class,
            ReportSeeder::class,
            DashboardMetricSeeder::class,
        ]);
    }
}
