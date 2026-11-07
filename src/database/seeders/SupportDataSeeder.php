<?php

namespace Database\Seeders;

use App\Models\ImmediateSupport;
use App\Models\SupportFollowUp;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupportDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@bracmis.org')->first();
        $users = User::pluck('id', 'email');

        $supports = [
            // Immediate support
            ['key' => 'food_fatima_feb', 'support_type' => 'food', 'email' => 'fatima@bracmis.org', 'support_date' => '2026-02-11', 'amount' => 1500, 'status' => 'provided', 'description' => 'Monthly dry food package for a family of four.'],
            ['key' => 'bus_rahim_mar', 'support_type' => 'bus_ticket', 'email' => 'rahim@bracmis.org', 'support_date' => '2026-03-05', 'amount' => 650, 'status' => 'provided', 'description' => 'Bus ticket to return to the home district.'],
            ['key' => 'shelter_ayesha_apr', 'support_type' => 'shelter', 'email' => 'ayesha@bracmis.org', 'support_date' => '2026-04-18', 'amount' => 3000, 'status' => 'provided', 'description' => 'Three nights of temporary shelter near the support centre.'],
            ['key' => 'food_kabir_may', 'support_type' => 'food', 'email' => 'kabir@bracmis.org', 'support_date' => '2026-05-22', 'amount' => 1200, 'status' => 'provided', 'description' => 'Emergency food package after arrival.'],
            ['key' => 'bus_shahida_jun', 'support_type' => 'bus_ticket', 'email' => 'shahida@bracmis.org', 'support_date' => '2026-06-14', 'amount' => 500, 'status' => 'provided', 'description' => 'Bus fare to attend a skills assessment.'],
            ['key' => 'shelter_jamil_jul', 'support_type' => 'shelter', 'email' => 'jamil@bracmis.org', 'support_date' => '2026-07-09', 'amount' => 2500, 'status' => 'provided', 'description' => 'Short-term shelter support while documents were processed.'],
            ['key' => 'food_nazma_aug', 'support_type' => 'food', 'email' => 'nazma@bracmis.org', 'support_date' => '2026-08-21', 'amount' => 1100, 'status' => 'provided', 'description' => 'Food package for the recipient and two dependants.'],
            ['key' => 'food_mizanur_sep', 'support_type' => 'food', 'email' => 'mizanur@bracmis.org', 'support_date' => '2026-09-28', 'amount' => 900, 'status' => 'provided', 'description' => 'One-week food support.'],
            ['key' => 'food_fatima_oct', 'support_type' => 'food', 'email' => 'fatima@bracmis.org', 'support_date' => '2026-10-12', 'amount' => 1500, 'status' => 'provided', 'description' => 'Food support during follow-up period.'],
            ['key' => 'bus_rahim_oct', 'support_type' => 'bus_ticket', 'email' => 'rahim@bracmis.org', 'support_date' => '2026-10-18', 'amount' => 800, 'status' => 'provided', 'description' => 'Travel support to a job interview.'],
            ['key' => 'shelter_ayesha_oct', 'support_type' => 'shelter', 'email' => 'ayesha@bracmis.org', 'support_date' => '2026-10-25', 'amount' => 3000, 'status' => 'provided', 'description' => 'Transitional shelter for one week.'],
            ['key' => 'food_kabir_nov', 'support_type' => 'food', 'email' => 'kabir@bracmis.org', 'support_date' => '2026-11-01', 'amount' => 1200, 'status' => 'provided', 'description' => 'Food package after returning from overseas.'],
            ['key' => 'bus_shahida_nov', 'support_type' => 'bus_ticket', 'email' => 'shahida@bracmis.org', 'support_date' => '2026-11-03', 'amount' => 650, 'status' => 'pending', 'description' => 'Bus fare request awaiting approval.'],
            ['key' => 'shelter_jamil_nov', 'support_type' => 'shelter', 'email' => 'jamil@bracmis.org', 'support_date' => '2026-11-04', 'amount' => 2500, 'status' => 'provided', 'description' => 'Emergency shelter support.'],
            ['key' => 'food_momena_oct', 'support_type' => 'food', 'recipient_name' => 'Momena Khatun', 'recipient_phone' => '01711000101', 'support_date' => '2026-10-30', 'amount' => 900, 'status' => 'provided', 'description' => 'Walk-in recipient food support.'],
            ['key' => 'bus_sujon_nov', 'support_type' => 'bus_ticket', 'recipient_name' => 'Sujon Mia', 'recipient_phone' => '01711000102', 'support_date' => '2026-11-05', 'amount' => 500, 'status' => 'provided', 'description' => 'Walk-in bus ticket support.'],
            ['key' => 'shelter_rokeya_oct', 'support_type' => 'shelter', 'recipient_name' => 'Rokeya Begum', 'recipient_phone' => '01711000103', 'support_date' => '2026-10-08', 'amount' => 2000, 'status' => 'provided', 'description' => 'Temporary shelter for a walk-in recipient.'],

            // Mental / psychological support
            ['key' => 'mental_nazma_apr', 'support_type' => 'mental_psychological', 'email' => 'nazma@bracmis.org', 'support_date' => '2026-04-05', 'amount' => 2000, 'status' => 'provided', 'description' => 'Counselling session with a psychological support officer.'],
            ['key' => 'mental_mizanur_jul', 'support_type' => 'mental_psychological', 'email' => 'mizanur@bracmis.org', 'support_date' => '2026-07-19', 'amount' => 1800, 'status' => 'provided', 'description' => 'Follow-up counselling and psychosocial assessment.'],
            ['key' => 'mental_fatima_oct', 'support_type' => 'mental_psychological', 'email' => 'fatima@bracmis.org', 'support_date' => '2026-10-20', 'amount' => 2200, 'status' => 'provided', 'description' => 'Trauma counselling session.'],
            ['key' => 'mental_rahim_nov', 'support_type' => 'mental_psychological', 'email' => 'rahim@bracmis.org', 'support_date' => '2026-11-02', 'amount' => 1800, 'status' => 'pending', 'description' => 'Requested counselling session awaiting scheduling.'],

            // Physical support
            ['key' => 'physical_ayesha_mar', 'support_type' => 'physical', 'email' => 'ayesha@bracmis.org', 'support_date' => '2026-03-25', 'amount' => 4500, 'status' => 'provided', 'description' => 'Medical check-up and medicine support.'],
            ['key' => 'physical_jamil_jun', 'support_type' => 'physical', 'email' => 'jamil@bracmis.org', 'support_date' => '2026-06-12', 'amount' => 3200, 'status' => 'provided', 'description' => 'Physiotherapy sessions for a work-related injury.'],
            ['key' => 'physical_fatima_oct', 'support_type' => 'physical', 'email' => 'fatima@bracmis.org', 'support_date' => '2026-10-28', 'amount' => 4500, 'status' => 'provided', 'description' => 'Hospital follow-up and medication.'],
            ['key' => 'physical_rahim_nov', 'support_type' => 'physical', 'email' => 'rahim@bracmis.org', 'support_date' => '2026-11-05', 'amount' => 3200, 'status' => 'provided', 'description' => 'Treatment support for a chronic condition.'],

            // Financial support
            ['key' => 'financial_kabir_jan', 'support_type' => 'financial', 'email' => 'kabir@bracmis.org', 'support_date' => '2026-01-15', 'amount' => 10000, 'status' => 'provided', 'description' => 'Seed capital for a small tailoring business.'],
            ['key' => 'financial_shahida_mar', 'support_type' => 'financial', 'email' => 'shahida@bracmis.org', 'support_date' => '2026-03-10', 'amount' => 8000, 'status' => 'provided', 'description' => 'Start-up grant for a home-based sewing unit.'],
            ['key' => 'financial_nazma_may', 'support_type' => 'financial', 'email' => 'nazma@bracmis.org', 'support_date' => '2026-05-20', 'amount' => 12000, 'status' => 'provided', 'description' => 'Livelihood grant for a small grocery shop.'],
            ['key' => 'financial_mizanur_jul', 'support_type' => 'financial', 'email' => 'mizanur@bracmis.org', 'support_date' => '2026-07-14', 'amount' => 9000, 'status' => 'provided', 'description' => 'Tools and equipment grant for carpentry work.'],
            ['key' => 'financial_ayesha_sep', 'support_type' => 'financial', 'email' => 'ayesha@bracmis.org', 'support_date' => '2026-09-15', 'amount' => 7500, 'status' => 'pending', 'description' => 'Livelihood grant request under review.'],
            ['key' => 'financial_kabir_oct', 'support_type' => 'financial', 'email' => 'kabir@bracmis.org', 'support_date' => '2026-10-05', 'amount' => 6000, 'status' => 'provided', 'description' => 'Additional working capital for metal fabrication.'],
            ['key' => 'financial_jamil_nov', 'support_type' => 'financial', 'email' => 'jamil@bracmis.org', 'support_date' => '2026-11-01', 'amount' => 5000, 'status' => 'cancelled', 'description' => 'Application cancelled after the recipient found employment.'],
        ];

        $createdSupports = [];

        foreach ($supports as $data) {
            $email = $data['email'] ?? null;
            $userId = $email ? ($users[$email] ?? null) : null;

            $record = ImmediateSupport::firstOrCreate(
                [
                    'support_type' => $data['support_type'],
                    'support_date' => $data['support_date'],
                    'user_id' => $userId,
                    'recipient_name' => $userId ? null : ($data['recipient_name'] ?? null),
                ],
                [
                    'recipient_phone' => $data['recipient_phone'] ?? null,
                    'amount' => $data['amount'] ?? null,
                    'status' => $data['status'],
                    'description' => $data['description'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'provided_by' => $admin?->id,
                ]
            );

            $createdSupports[$data['key']] = $record;
        }

        $followUps = [
            ['support' => 'food_fatima_feb', 'follow_up_date' => '2026-02-20', 'method' => 'phone_call', 'status' => 'completed', 'outcome' => 'Family confirmed receiving the food package.', 'next_follow_up_date' => '2026-03-20'],
            ['support' => 'shelter_ayesha_apr', 'follow_up_date' => '2026-04-28', 'method' => 'home_visit', 'status' => 'completed', 'outcome' => 'Shelter arrangement is stable and the family has settled.'],
            ['support' => 'food_kabir_may', 'follow_up_date' => '2026-06-01', 'method' => 'office_visit', 'status' => 'completed', 'outcome' => 'Recipient enrolled in a skills training programme.'],
            ['support' => 'mental_mizanur_jul', 'follow_up_date' => '2026-07-28', 'method' => 'home_visit', 'status' => 'completed', 'outcome' => 'Recipient reports improved wellbeing and has resumed work.', 'is_reintegrated' => true],
            ['support' => 'food_nazma_aug', 'follow_up_date' => '2026-09-05', 'method' => 'community_visit', 'status' => 'missed', 'notes' => 'Recipient was unavailable at the time of the visit.'],
            ['support' => 'food_fatima_oct', 'follow_up_date' => '2026-10-20', 'method' => 'phone_call', 'status' => 'completed', 'outcome' => 'Food support received; family situation improving.'],
            ['support' => 'shelter_ayesha_oct', 'follow_up_date' => '2026-11-01', 'method' => 'home_visit', 'status' => 'completed', 'outcome' => 'Recipient has moved to permanent accommodation.'],
            ['support' => 'food_kabir_nov', 'follow_up_date' => '2026-11-07', 'method' => 'office_visit', 'status' => 'scheduled', 'notes' => 'Discuss next steps for employment.'],
            ['support' => 'mental_fatima_oct', 'follow_up_date' => '2026-11-08', 'method' => 'phone_call', 'status' => 'scheduled'],
            ['support' => 'mental_rahim_nov', 'follow_up_date' => '2026-11-09', 'method' => 'office_visit', 'status' => 'scheduled'],
            ['support' => 'financial_kabir_jan', 'follow_up_date' => '2026-02-01', 'method' => 'phone_call', 'status' => 'completed', 'outcome' => 'Business is running and monthly income has increased.'],
            ['support' => 'financial_ayesha_sep', 'follow_up_date' => '2026-09-25', 'method' => 'home_visit', 'status' => 'scheduled', 'notes' => 'Verify business plan before disbursement.'],
            ['support' => 'financial_jamil_nov', 'follow_up_date' => '2026-11-03', 'method' => 'phone_call', 'status' => 'missed', 'notes' => 'Recipient did not answer the call.'],
        ];

        $createdFollowUps = 0;

        foreach ($followUps as $data) {
            $support = $createdSupports[$data['support']] ?? null;

            if (! $support) {
                continue;
            }

            $followUp = SupportFollowUp::firstOrCreate(
                [
                    'immediate_support_id' => $support->id,
                    'follow_up_date' => $data['follow_up_date'],
                    'method' => $data['method'],
                ],
                [
                    'status' => $data['status'],
                    'is_reintegrated' => $data['is_reintegrated'] ?? false,
                    'reintegrated_at' => ($data['is_reintegrated'] ?? false) ? now() : null,
                    'outcome' => $data['outcome'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'next_follow_up_date' => $data['next_follow_up_date'] ?? null,
                    'followed_by' => $admin?->id,
                ]
            );

            if ($followUp->wasRecentlyCreated) {
                $createdFollowUps++;
            }
        }

        $this->command?->info('Created '.count($createdSupports).' support records and '.$createdFollowUps.' follow-ups.');
    }
}
