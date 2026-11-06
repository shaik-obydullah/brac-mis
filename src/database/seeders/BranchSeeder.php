<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $headOffice = Branch::firstOrCreate(
            ['code' => 'HO-001'],
            [
                'name' => 'Head Office',
                'type' => Branch::TYPE_HEAD_OFFICE,
                'parent_id' => null,
                'district' => 'Dhaka',
                'division' => 'Dhaka',
                'status' => true,
            ]
        );

        $branches = [
            ['name' => 'Dhaka Main Branch', 'code' => 'DMB-001', 'district' => 'Dhaka', 'division' => 'Dhaka', 'status' => true],
            ['name' => 'Chittagong Branch', 'code' => 'CTG-001', 'district' => 'Chittagong', 'division' => 'Chittagong', 'status' => true],
            ['name' => 'Sylhet Branch', 'code' => 'SYL-001', 'district' => 'Sylhet', 'division' => 'Sylhet', 'status' => true],
            ['name' => 'Rajshahi Branch', 'code' => 'RJS-001', 'district' => 'Rajshahi', 'division' => 'Rajshahi', 'status' => true],
            ['name' => 'Khulna Branch', 'code' => 'KHL-001', 'district' => 'Khulna', 'division' => 'Khulna', 'status' => true],
        ];

        foreach ($branches as $branch) {
            Branch::firstOrCreate(
                ['code' => $branch['code']],
                $branch + [
                    'type' => Branch::TYPE_DISTRICT,
                    'parent_id' => $headOffice->id,
                ]
            );
        }

        $this->command->info('Created '.(count($branches) + 1).' branches.');
    }
}
