<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\StaffIncentive;
use Illuminate\Database\Seeder;

class StaffIncentiveSeeder extends Seeder
{
    public function run(): void
    {
        $staff = Staff::orderBy('id')->take(5)->get();

        if ($staff->isEmpty()) {
            return;
        }

        $incentives = [
            ['title' => 'Performance bonus', 'amount' => 5000, 'status' => StaffIncentive::STATUS_PAID, 'notes' => 'Quarterly performance recognition.'],
            ['title' => 'Festival bonus', 'amount' => 7500, 'status' => StaffIncentive::STATUS_PAID, 'notes' => null],
            ['title' => 'Field travel allowance', 'amount' => 2500, 'status' => StaffIncentive::STATUS_PENDING, 'notes' => 'Pending approval from branch manager.'],
            ['title' => 'Attendance incentive', 'amount' => 1500, 'status' => StaffIncentive::STATUS_PAID, 'notes' => null],
            ['title' => 'Program milestone reward', 'amount' => 4000, 'status' => StaffIncentive::STATUS_PENDING, 'notes' => 'Awarded for successful profile milestone.'],
        ];

        foreach ($incentives as $index => $incentive) {
            $member = $staff[$index % $staff->count()];

            StaffIncentive::firstOrCreate(
                [
                    'staff_id' => $member->id,
                    'title' => $incentive['title'],
                ],
                $incentive + [
                    'incentive_date' => now()->startOfMonth()->addDays($index * 3)->toDateString(),
                    'created_by' => $member->user_id,
                ]
            );
        }

        $this->command->info('Created '.count($incentives).' staff incentives.');
    }
}
