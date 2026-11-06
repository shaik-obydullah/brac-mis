<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class SampleUpazilaBranchSeeder extends Seeder
{
    /**
     * A few sample upazila branches under each seeded district branch.
     */
    private const SAMPLES = [
        'Dhaka' => [
            'prefix' => 'DHK',
            'upazilas' => ['Savar', 'Keraniganj', 'Dhamrai'],
        ],
        'Chittagong' => [
            'prefix' => 'CTG',
            'upazilas' => ['Sitakunda', 'Patiya', 'Hathazari'],
        ],
        'Sylhet' => [
            'prefix' => 'SYL',
            'upazilas' => ['Sylhet Sadar', 'Beanibazar', 'Golapganj'],
        ],
        'Rajshahi' => [
            'prefix' => 'RJS',
            'upazilas' => ['Paba', 'Godagari', 'Puthia'],
        ],
        'Khulna' => [
            'prefix' => 'KHL',
            'upazilas' => ['Dumuria', 'Rupsha', 'Paikgasa'],
        ],
    ];

    public function run(): void
    {
        $created = 0;

        foreach (self::SAMPLES as $district => $config) {
            $parent = Branch::where('type', Branch::TYPE_DISTRICT)
                ->where('district', $district)
                ->orderBy('id')
                ->first();

            if (! $parent) {
                $this->command?->warn("No district branch found for {$district}; skipping its upazilas.");

                continue;
            }

            foreach ($config['upazilas'] as $index => $upazila) {
                $branch = Branch::firstOrCreate(
                    ['code' => sprintf('UPZ-%s-%02d', $config['prefix'], $index + 1)],
                    [
                        'name' => $upazila.' Upazila Branch',
                        'type' => Branch::TYPE_UPAZILA,
                        'parent_id' => $parent->id,
                        'division' => $parent->division,
                        'district' => $parent->district,
                        'upazila' => $upazila,
                        'status' => true,
                    ]
                );

                if ($branch->wasRecentlyCreated) {
                    $created++;
                }
            }
        }

        $this->command?->info("Created {$created} sample upazila branches.");
    }
}
