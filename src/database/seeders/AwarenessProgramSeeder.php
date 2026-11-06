<?php

namespace Database\Seeders;

use App\Models\AwarenessProgram;
use App\Models\User;
use Illuminate\Database\Seeder;

class AwarenessProgramSeeder extends Seeder
{
    /**
     * Sample awareness programs with participant outcomes.
     */
    private const PROGRAMS = [
        [
            'program_name' => 'Anti-Trafficking Awareness Session',
            'division' => 'Dhaka',
            'district' => 'Dhaka',
            'upazila' => 'Savar',
            'address' => 'Savar Union Parishad Hall',
            'outcomes' => [true, true, true, true, true, false],
        ],
        [
            'program_name' => 'Safe Migration Orientation',
            'division' => 'Dhaka',
            'district' => 'Gazipur',
            'upazila' => 'Gazipur Sadar',
            'address' => 'Gazipur Community Centre',
            'outcomes' => [true, true, true, true, false],
        ],
        [
            'program_name' => 'Child Rights Awareness Program',
            'division' => 'Chattagram',
            'district' => 'Chattogram',
            'upazila' => 'Patiya',
            'address' => 'Patiya Government High School',
            'outcomes' => [true, true, true, false, false, false],
        ],
        [
            'program_name' => "Women's Safety & Legal Awareness",
            'division' => 'Khulna',
            'district' => 'Khulna',
            'upazila' => 'Dumuria',
            'address' => 'Dumuria Women Development Centre',
            'outcomes' => [true, true, true, true],
        ],
        [
            'program_name' => 'Community Health Awareness Campaign',
            'division' => 'Rajshahi',
            'district' => 'Rajshahi',
            'upazila' => 'Paba',
            'address' => 'Paba Upazila Health Complex',
            'outcomes' => [true, true, false, false, false],
        ],
        [
            'program_name' => 'Digital Safety Workshop',
            'division' => 'Sylhet',
            'district' => 'Sylhet',
            'upazila' => 'Sylhet Sadar',
            'address' => 'Zindabazar Community Hall',
            'outcomes' => [true, true, true, false, false],
        ],
        [
            'program_name' => 'Climate Resilience Awareness',
            'division' => 'Barisal',
            'district' => 'Patuakhali',
            'upazila' => 'Kalapara',
            'address' => 'Kalapara Cyclone Shelter',
            'outcomes' => [true, true, true, false],
        ],
        [
            'program_name' => 'Financial Literacy Session',
            'division' => 'Rangpur',
            'district' => 'Rangpur',
            'upazila' => 'Rangpur Sadar',
            'address' => 'Rangpur Town Hall',
            'outcomes' => [true, true, true, true, false],
        ],
        [
            'program_name' => 'Road Safety Awareness Program',
            'division' => 'Chattagram',
            'district' => 'Comilla',
            'upazila' => 'Comilla Sadar',
            'address' => 'Comilla District Stadium',
            'outcomes' => [],
        ],
        [
            'program_name' => 'Hygiene & Sanitation Campaign',
            'division' => 'Mymensingh',
            'district' => 'Mymensingh',
            'upazila' => 'Mymensingh Sadar',
            'address' => 'Mymensingh Community Centre',
            'outcomes' => [],
        ],
    ];

    public function run(): void
    {
        $createdBy = User::orderBy('id')->value('id');
        $created = 0;
        $participants = 0;

        foreach (self::PROGRAMS as $data) {
            $outcomes = $data['outcomes'];
            unset($data['outcomes']);

            $program = AwarenessProgram::firstOrCreate(
                [
                    'program_name' => $data['program_name'],
                    'division' => $data['division'],
                    'district' => $data['district'],
                    'upazila' => $data['upazila'],
                ],
                [
                    'address' => $data['address'],
                    'created_by' => $createdBy,
                ]
            );

            if (! $program->wasRecentlyCreated) {
                continue;
            }

            $created++;

            foreach ($outcomes as $index => $understood) {
                $program->outcomes()->create([
                    'participant_name' => 'Participant '.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'participant_phone' => null,
                    'understood' => $understood,
                    'notes' => $understood
                        ? 'Understood the key messages.'
                        : 'Needs a follow-up session.',
                ]);

                $participants++;
            }
        }

        $this->command?->info("Created {$created} awareness programs with {$participants} participant outcomes.");
    }
}
