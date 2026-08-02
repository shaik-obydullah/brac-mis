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
            ['updated', 'App\Models\Beneficiary', 'Beneficiary profile updated', ['status' => 'active'], ['status' => 'inactive']],
            ['follow_up_scheduled', 'App\Models\BeneficiaryFollowUp', 'Follow-up scheduled', ['status' => 'pending'], ['status' => 'scheduled']],
            ['created', 'App\Models\Migrant', 'Migrant registered', ['status' => null], ['status' => 'registered']],
            ['updated', 'App\Models\Migrant', 'Migrant deployed to destination', ['status' => 'registered'], ['status' => 'deployed']],
            ['contract_signed', 'App\Models\MigrantDestination', 'Employment contract recorded', ['status' => 'pending'], ['status' => 'active']],
            ['created', 'App\Models\Returnee', 'Returnee registered', ['status' => null], ['current_status' => 'assessed']],
            ['updated', 'App\Models\Returnee', 'Reintegration plan status updated', ['status' => 'draft'], ['status' => 'active']],
            ['livelihood_grant_disbursed', 'App\Models\ReturneeLivelihoodSupport', 'Livelihood grant disbursed', ['status' => 'pending'], ['status' => 'active']],
        ];

        foreach ($logs as $i => [$action, $subjectType, $subjectId, $oldValues, $newValues]) {
            $actor = $staffUsers->isEmpty() ? $user : $staffUsers->random();

            AuditLog::firstOrCreate(
                ['user_id' => $actor->id, 'action' => $action, 'subject_type' => $subjectType, 'subject_id' => $i + 1],
                [
                    'old_values' => $oldValues,
                    'new_values' => $newValues,
                    'ip_address' => '103.129.130.' . rand(2, 254),
                    'created_at' => now()->subDays(rand(1, 30)),
                ]
            );
        }

        $this->command->info('Created ' . count($logs) . ' audit logs.');
    }
}
