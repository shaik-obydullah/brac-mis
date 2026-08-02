<?php

namespace Database\Seeders;

use App\Models\Beneficiary;
use App\Models\BeneficiaryDocument;
use App\Models\BeneficiaryFollowUp;
use App\Models\BeneficiaryHousehold;
use App\Models\BeneficiaryIntervention;
use App\Models\Branch;
use App\Models\FollowUpType;
use App\Models\InterventionType;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;

class BeneficiarySeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();
        $staff = Staff::all();
        $fieldOfficers = User::whereHas('roles', fn($q) => $q->where('name', 'field-officer'))->get();

        FollowUpType::firstOrCreate(['name' => 'Home Visit']);
        FollowUpType::firstOrCreate(['name' => 'Phone Call']);
        FollowUpType::firstOrCreate(['name' => 'Office Visit']);

        InterventionType::firstOrCreate(['name' => 'Skill Training']);
        InterventionType::firstOrCreate(['name' => 'Financial Support']);
        InterventionType::firstOrCreate(['name' => 'Counseling']);
        InterventionType::firstOrCreate(['name' => 'Medical Assistance']);
        InterventionType::firstOrCreate(['name' => 'Legal Aid']);

        $beneficiaries = [
            ['BRAC-BEN-001', 'Mohammad Ali', 'male', '1990-05-15', '1234567890', '+880-1711111111', '12/1 Dhanmondi, Dhaka', 'Rickshaw Puller', 15000, 4, 'active'],
            ['BRAC-BEN-002', 'Shahina Begum', 'female', '1985-08-22', '1234567891', '+880-1711111112', '45 Mirpur Road, Dhaka', 'Housewife', 8000, 3, 'active'],
            ['BRAC-BEN-003', 'Abdul Karim', 'male', '1978-12-03', '1234567892', '+880-1711111113', '78 Farmgate, Dhaka', 'Day Laborer', 10000, 5, 'active'],
            ['BRAC-BEN-004', 'Rashida Akter', 'female', '1992-03-18', '1234567893', '+880-1711111114', '23/2 Motijheel, Dhaka', 'Garment Worker', 12000, 2, 'active'],
            ['BRAC-BEN-005', 'Nurul Islam', 'male', '1982-07-09', '1234567894', '+880-1811111111', '56 Station Road, Chittagong', 'Fisherman', 18000, 6, 'active'],
            ['BRAC-BEN-006', 'Jahanara Begum', 'female', '1995-11-25', '1234567895', '+880-1811111112', '89 Pahartali, Chittagong', 'Small Business', 20000, 3, 'active'],
            ['BRAC-BEN-007', 'Khalilur Rahman', 'male', '1975-01-30', '1234567896', '+880-1911111111', '34 Zindabazar, Sylhet', 'Tea Garden Worker', 14000, 7, 'active'],
            ['BRAC-BEN-008', 'Shamima Sultana', 'female', '1988-06-14', '1234567897', '+880-1911111112', '67 Amberkhana, Sylhet', 'NGO Worker', 16000, 4, 'active'],
            ['BRAC-BEN-009', 'Rafiq Uddin', 'male', '1970-09-20', '1234567898', '+880-2011111111', '12 Shaheb Bazar, Rajshahi', 'Farmer', 12000, 5, 'inactive'],
            ['BRAC-BEN-010', 'Parvin Akter', 'female', '1993-04-10', '1234567899', '+880-2111111111', '78 Khan Jahan Ali Road, Khulna', 'Homemaker', 7000, 3, 'active'],
        ];

        foreach ($beneficiaries as [$bracId, $name, $gender, $dob, $nid, $phone, $address, $occ, $income, $familySize, $status]) {
            $branch = $branches->firstWhere('district', explode(',', $address)[1] ?? 'Dhaka') ?? $branches->first();
            $user = $fieldOfficers->random();

            $beneficiary = Beneficiary::firstOrCreate(
                ['brac_id' => $bracId],
                [
                    'name' => $name,
                    'gender' => $gender,
                    'date_of_birth' => $dob,
                    'nid_number' => $nid,
                    'phone' => $phone,
                    'address_line_1' => $address,
                    'occupation' => $occ,
                    'monthly_income' => $income,
                    'family_size' => $familySize,
                    'status' => $status,
                    'branch_id' => $branch->id,
                    'created_by' => $user->id,
                ]
            );

            BeneficiaryHousehold::firstOrCreate(
                ['beneficiary_id' => $beneficiary->id, 'member_name' => 'Spouse'],
                [
                    'relationship' => 'Spouse',
                    'age' => rand(25, 50),
                    'occupation' => 'Housewife',
                    'monthly_income' => 0,
                ]
            );

            BeneficiaryIntervention::create([
                'beneficiary_id' => $beneficiary->id,
                'type' => 'Skill Training',
                'start_date' => now()->subMonths(rand(1, 6)),
                'end_date' => now()->subDays(rand(1, 30)),
                'status' => 'completed',
                'notes' => 'Training completed successfully.',
                'created_by' => $user->id,
            ]);

            BeneficiaryFollowUp::create([
                'beneficiary_id' => $beneficiary->id,
                'staff_id' => $staff->random()->id,
                'type' => 'Home Visit',
                'date' => now()->subDays(rand(5, 30)),
                'notes' => 'Follow-up completed. Family situation stable.',
                'next_date' => now()->addMonths(1),
                'status' => 'completed',
            ]);

            BeneficiaryDocument::create([
                'beneficiary_id' => $beneficiary->id,
                'type' => 'NID',
                'file_path' => 'documents/beneficiaries/' . $bracId . '/nid.pdf',
            ]);
        }

        $this->command->info('Created ' . count($beneficiaries) . ' beneficiaries with households, interventions, and follow-ups.');
    }
}
