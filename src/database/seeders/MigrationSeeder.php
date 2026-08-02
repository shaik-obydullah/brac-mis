<?php

namespace Database\Seeders;

use App\Models\Beneficiary;
use App\Models\Country;
use App\Models\Migrant;
use App\Models\MigrantDestination;
use App\Models\MigrantDocument;
use App\Models\MigrantFinancialRecord;
use Illuminate\Database\Seeder;

class MigrationSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'Saudi Arabia', 'code' => 'SAU', 'currency' => 'SAR', 'status' => true],
            ['name' => 'United Arab Emirates', 'code' => 'ARE', 'currency' => 'AED', 'status' => true],
            ['name' => 'Qatar', 'code' => 'QAT', 'currency' => 'QAR', 'status' => true],
            ['name' => 'Kuwait', 'code' => 'KWT', 'currency' => 'KWD', 'status' => true],
            ['name' => 'Oman', 'code' => 'OMN', 'currency' => 'OMR', 'status' => true],
            ['name' => 'Bahrain', 'code' => 'BHR', 'currency' => 'BHD', 'status' => true],
            ['name' => 'Malaysia', 'code' => 'MYS', 'currency' => 'MYR', 'status' => true],
            ['name' => 'Singapore', 'code' => 'SGP', 'currency' => 'SGD', 'status' => true],
            ['name' => 'Italy', 'code' => 'ITA', 'currency' => 'EUR', 'status' => true],
            ['name' => 'United Kingdom', 'code' => 'GBR', 'currency' => 'GBP', 'status' => true],
        ];

        foreach ($countries as $c) {
            Country::firstOrCreate(['code' => $c['code']], $c);
        }

        $beneficiaries = Beneficiary::all();

        $migrantData = [
            ['BRAC-MIG-001', 'Kamal Hossain', 'male', '1992-03-15', '1234567890', '+880-1711111111', 'AB123456', 'Saudi Arabia', 'Riyadh', 'intermediate', 'SSC', 'Driver', 'deployed'],
            ['BRAC-MIG-002', 'Farida Yesmin', 'female', '1995-07-22', '1234567891', '+880-1711111112', 'CD789012', 'United Arab Emirates', 'Dubai', 'basic', 'HSC', 'Domestic Worker', 'deployed'],
            ['BRAC-MIG-003', 'Shahidul Islam', 'male', '1988-11-08', '1234567892', '+880-1711111113', 'EF345678', 'Malaysia', 'Kuala Lumpur', 'advanced', 'Bachelor', 'Software Engineer', 'deployed'],
            ['BRAC-MIG-004', 'Rokeya Begum', 'female', '1990-02-14', '1234567893', '+880-1711111114', 'GH901234', 'Qatar', 'Doha', 'intermediate', 'SSC', 'Nurse', 'pre_departure'],
            ['BRAC-MIG-005', 'Jahangir Alam', 'male', '1985-06-30', '1234567894', '+880-1811111111', null, 'Kuwait', 'Kuwait City', 'basic', 'Class 8', 'Laborer', 'registered'],
            ['BRAC-MIG-006', 'Nasrin Akter', 'female', '1993-09-05', '1234567895', '+880-1811111112', 'IJ567890', 'Saudi Arabia', 'Jeddah', 'intermediate', 'HSC', 'Teacher', 'deployed'],
            ['BRAC-MIG-007', 'Mofij Uddin', 'male', '1980-12-20', '1234567896', '+880-1911111111', 'KL123456', 'Oman', 'Muscat', 'advanced', 'Bachelor', 'Engineer', 'deployed'],
            ['BRAC-MIG-008', 'Shahnaz Parvin', 'female', '1996-04-12', '1234567897', '+880-1911111112', null, 'Singapore', 'Singapore', 'intermediate', 'HSC', 'Nanny', 'cancelled'],
            ['BRAC-MIG-009', 'Abul Kashem', 'male', '1982-08-25', '1234567898', '+880-2011111111', 'MN789012', 'Italy', 'Rome', 'basic', 'Class 5', 'Farm Worker', 'pre_departure'],
            ['BRAC-MIG-010', 'Taslima Khatun', 'female', '1991-01-18', '1234567899', '+880-2111111111', 'OP345678', 'United Arab Emirates', 'Abu Dhabi', 'basic', 'SSC', 'Housekeeper', 'deployed'],
        ];

        foreach ($migrantData as [$migId, $name, $gender, $dob, $nid, $phone, $passport, $destCountry, $destCity, $skill, $education, $occupation, $status]) {
            $country = Country::where('name', $destCountry)->first();
            $beneficiary = $beneficiaries->random();

            $migrant = Migrant::firstOrCreate(
                ['brac_id' => $migId],
                [
                    'name' => $name,
                    'gender' => $gender,
                    'date_of_birth' => $dob,
                    'nid_number' => $nid,
                    'phone' => $phone,
                    'passport_number' => $passport,
                    'origin_district_id' => null,
                    'origin_upazila_id' => null,
                    'destination_country_id' => $country?->id,
                    'destination_city' => $destCity,
                    'skill_level' => $skill,
                    'education_level' => $education,
                    'occupation' => $occupation,
                    'status' => $status,
                    'beneficiary_id' => $beneficiary->id,
                ]
            );

            MigrantDestination::firstOrCreate(
                ['migrant_id' => $migrant->id, 'country_id' => $country?->id],
                [
                    'city' => $destCity,
                    'employer_name' => 'Employer of ' . $name,
                    'employer_contact' => '+966-5' . rand(10000000, 99999999),
                    'contract_start' => $status === 'deployed' ? now()->subMonths(rand(3, 12)) : null,
                    'contract_end' => $status === 'deployed' ? now()->subMonths(rand(3, 12))->addYears(2) : null,
                    'salary_amount' => rand(800, 2500),
                    'salary_currency' => $country?->currency ?? 'USD',
                    'status' => in_array($status, ['deployed', 'pre_departure']) ? 'active' : 'completed',
                ]
            );

            MigrantDocument::firstOrCreate(
                ['migrant_id' => $migrant->id, 'type' => 'Passport'],
                [
                    'file_path' => 'documents/migrants/' . $migId . '/passport.pdf',
                    'expiry_date' => now()->addYears(rand(2, 5)),
                ]
            );

            MigrantFinancialRecord::firstOrCreate(
                ['migrant_id' => $migrant->id, 'type' => 'migration_cost', 'description' => 'Migration cost for ' . $name],
                [
                    'amount' => rand(200000, 600000),
                    'currency' => 'BDT',
                    'date' => now()->subMonths(rand(3, 12)),
                ]
            );
        }

        $this->command->info('Created ' . count($migrantData) . ' migrants with destinations, documents, and financial records.');
    }
}
