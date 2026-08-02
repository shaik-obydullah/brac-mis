<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();
        $admin = User::where('email', 'admin@bracmis.org')->first();

        Staff::firstOrCreate(
            ['employee_id' => 'BRAC-001'],
            [
                'user_id' => $admin->id,
                'branch_id' => $branches->first()->id,
                'designation' => 'Regional Manager',
                'phone' => '+880-1712345678',
            ]
        );

        $staffData = [
            ['BRAC-002', 'Dhaka', 'Branch Manager', 'Fatima Begum', 'fatima@bracmis.org', '+880-1712345679'],
            ['BRAC-003', 'Dhaka', 'Senior Officer', 'Rahim Uddin', 'rahim@bracmis.org', '+880-1712345680'],
            ['BRAC-004', 'Dhaka', 'Field Officer', 'Ayesha Khatun', 'ayesha@bracmis.org', '+880-1712345681'],
            ['BRAC-005', 'Chittagong', 'Branch Manager', 'Kabir Hossain', 'kabir@bracmis.org', '+880-1812345678'],
            ['BRAC-006', 'Chittagong', 'Officer', 'Shahida Parvin', 'shahida@bracmis.org', '+880-1812345679'],
            ['BRAC-007', 'Sylhet', 'Branch Manager', 'Jamil Ahmed', 'jamil@bracmis.org', '+880-1912345678'],
            ['BRAC-008', 'Rajshahi', 'Branch Manager', 'Nazma Akter', 'nazma@bracmis.org', '+880-2012345678'],
            ['BRAC-009', 'Khulna', 'Senior Officer', 'Mizanur Rahman', 'mizanur@bracmis.org', '+880-2112345678'],
        ];

        foreach ($staffData as [$empId, $branchDistrict, $desigName, $fullName, $email, $phone]) {
            $branch = $branches->firstWhere('district', $branchDistrict) ?? $branches->first();

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $fullName,
                    'password' => bcrypt('password'),
                    'phone' => $phone,
                    'branch_id' => $branch->id,
                    'status' => 'active',
                ]
            );
            $user->assignRole('field-officer');

            Staff::firstOrCreate(
                ['employee_id' => $empId],
                [
                    'user_id' => $user->id,
                    'branch_id' => $branch->id,
                    'designation' => $desigName,
                    'phone' => $phone,
                ]
            );
        }

        $this->command->info('Created ' . (count($staffData) + 1) . ' staff records with users.');
    }
}
