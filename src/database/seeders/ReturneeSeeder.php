<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Migrant;
use App\Models\Returnee;
use App\Models\ReturneeFollowUp;
use App\Models\ReturneeLivelihoodSupport;
use App\Models\ReturneeMicrofinance;
use App\Models\ReturneeReintegrationPlan;
use App\Models\ReturneeSkillAssessment;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class ReturneeSeeder extends Seeder
{
    public function run(): void
    {
        $staff = Staff::all();
        $migrants = Migrant::whereIn('status', ['returned', 'deployed'])->get();

        if ($migrants->isEmpty()) {
            $this->command->warn('No eligible migrants found for returnee seeding.');
            return;
        }

        $returneeData = [
            ['returnee' => 'completed', 'current_status' => 'assessed'],
            ['returnee' => 'completed', 'current_status' => 'planning'],
            ['returnee' => 'completed', 'current_status' => 'in_progress'],
            ['returnee' => 'completed', 'current_status' => 'completed'],
            ['returnee' => 'completed', 'current_status' => 'dropped'],
        ];

        foreach ($returneeData as $i => $data) {
            $migrant = $migrants->get($i) ?? $migrants->first();

            $returnee = Returnee::firstOrCreate(
                ['migrant_id' => $migrant->id],
                [
                    'beneficiary_id' => $migrant->beneficiary_id,
                    'return_date' => now()->subMonths(rand(1, 12)),
                    'return_reason' => 'Contract completed',
                    'origin_country_id' => $migrant->destination_country_id,
                    'current_status' => $data['current_status'],
                ]
            );

            ReturneeReintegrationPlan::firstOrCreate(
                ['returnee_id' => $returnee->id],
                [
                    'staff_id' => $staff->random()->id,
                    'goal' => 'Establish sustainable livelihood',
                    'activities' => 'Vocational training, capital support, business mentoring',
                    'timeline' => '6 months',
                    'status' => $data['current_status'] === 'completed' ? 'completed' : ($data['current_status'] === 'in_progress' ? 'active' : 'draft'),
                ]
            );

            ReturneeSkillAssessment::firstOrCreate(
                ['returnee_id' => $returnee->id, 'skill_name' => 'Vocational Skill'],
                [
                    'proficiency_level' => 'intermediate',
                    'certification' => rand(0, 1),
                    'assessed_by' => $staff->random()->name,
                    'assessed_date' => now()->subMonths(rand(1, 6)),
                ]
            );

            if (in_array($data['current_status'], ['in_progress', 'completed'])) {
                ReturneeLivelihoodSupport::firstOrCreate(
                    ['returnee_id' => $returnee->id, 'type' => 'grant'],
                    [
                        'amount' => rand(50000, 300000),
                        'provider' => 'BRAC',
                        'start_date' => now()->subMonths(rand(1, 6)),
                        'end_date' => now()->addMonths(rand(6, 12)),
                        'status' => $data['current_status'] === 'completed' ? 'completed' : 'active',
                    ]
                );

                ReturneeMicrofinance::firstOrCreate(
                    ['returnee_id' => $returnee->id],
                    [
                        'loan_amount' => rand(50000, 200000),
                        'loan_purpose' => 'Small business startup',
                        'disbursement_date' => now()->subMonths(rand(1, 6)),
                        'repayment_schedule' => 'Monthly installment over 12 months',
                        'status' => 'active',
                    ]
                );
            }

            ReturneeFollowUp::firstOrCreate(
                ['returnee_id' => $returnee->id, 'staff_id' => $staff->random()->id],
                [
                    'type' => 'Home Visit',
                    'date' => now()->subDays(rand(5, 30)),
                    'notes' => 'Returnee is adjusting well. ' . ($data['current_status'] === 'completed' ? 'Reintegration successful.' : 'Needs continued support.'),
                    'next_date' => now()->addMonths(1),
                    'status' => 'completed',
                ]
            );
        }

        $this->command->info('Created ' . count($returneeData) . ' returnees with plans, skill assessments, livelihood support, and microfinance.');
    }
}
