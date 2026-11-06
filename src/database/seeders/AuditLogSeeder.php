<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'admin@bracmis.org')->first();
        if (! $user) {
            $this->command->warn('No admin user found for audit log seeding.');

            return;
        }

        $staffUsers = User::whereHas('roles', fn ($q) => $q->where('name', 'field-officer'))->get();

        $logs = [
            ['created', 'App\Models\Branch', 'Branch record created', ['status' => 'active'], ['status' => 'active']],
            ['created', 'App\Models\Staff', 'Staff profile created', ['employee_id' => null], ['employee_id' => 'BRAC-002']],
            ['updated', 'App\Models\User', 'Profile updated', ['status' => 'active'], ['status' => 'inactive']],
            ['follow_up_scheduled', 'App\Models\SupportFollowUp', 'Follow-up scheduled', ['status' => 'pending'], ['status' => 'scheduled']],
            ['created', 'App\Models\User', 'Potential customer profile created', ['profile_type' => null], ['profile_type' => 'potential_customer']],
            ['updated', 'App\Models\User', 'Profile marked as potential customer', ['profile_type' => 'reintegrated'], ['profile_type' => 'potential_customer']],
            ['created', 'App\Models\UserSkill', 'Skill recorded', ['skill_name' => null], ['skill_name' => 'Tailoring']],
            ['created', 'App\Models\UserMigrationHistory', 'Migration history recorded', ['status' => null], ['status' => 'returned']],
            ['updated', 'App\Models\ImmediateSupport', 'Support record updated', ['status' => 'pending'], ['status' => 'provided']],
            ['updated', 'App\Models\SupportFollowUp', 'Profile marked as reintegrated', ['profile_type' => 'potential_customer'], ['profile_type' => 'reintegrated']],
        ];

        foreach ($logs as $i => [$action, $subjectType, $subjectId, $oldValues, $newValues]) {
            $actor = $staffUsers->isEmpty() ? $user : $staffUsers->random();

            AuditLog::firstOrCreate(
                ['user_id' => $actor->id, 'action' => $action, 'subject_type' => $subjectType, 'subject_id' => $i + 1],
                [
                    'old_values' => $oldValues,
                    'new_values' => $newValues,
                    'ip_address' => '103.129.130.'.rand(2, 254),
                    'created_at' => now()->subDays(rand(1, 30)),
                ]
            );
        }

        $this->command->info('Created '.count($logs).' audit logs.');
    }
}
