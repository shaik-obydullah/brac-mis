<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Database\Seeder;

class UserSkillSeeder extends Seeder
{
    public function run(): void
    {
        $profiles = [
            'fatima@bracmis.org' => [
                ['skill_name' => 'Tailoring', 'proficiency' => 'expert', 'years_of_experience' => 8, 'certification' => 'National Skills Certificate - Tailoring'],
                ['skill_name' => 'Quality Control', 'proficiency' => 'advanced', 'years_of_experience' => 5, 'certification' => null],
            ],
            'rahim@bracmis.org' => [
                ['skill_name' => 'Electrical Wiring', 'proficiency' => 'advanced', 'years_of_experience' => 6, 'certification' => 'Electrical Safety Certificate'],
                ['skill_name' => 'Solar Panel Installation', 'proficiency' => 'intermediate', 'years_of_experience' => 3, 'certification' => null],
            ],
            'ayesha@bracmis.org' => [
                ['skill_name' => 'Cooking & Catering', 'proficiency' => 'expert', 'years_of_experience' => 10, 'certification' => 'Food Safety & Hygiene Certificate'],
                ['skill_name' => 'Baking', 'proficiency' => 'intermediate', 'years_of_experience' => 4, 'certification' => null],
            ],
            'kabir@bracmis.org' => [
                ['skill_name' => 'Welding', 'proficiency' => 'expert', 'years_of_experience' => 9, 'certification' => '6G Welder Certification'],
                ['skill_name' => 'Metal Fabrication', 'proficiency' => 'advanced', 'years_of_experience' => 7, 'certification' => null],
            ],
            'shahida@bracmis.org' => [
                ['skill_name' => 'Sewing Machine Operation', 'proficiency' => 'advanced', 'years_of_experience' => 6, 'certification' => null],
                ['skill_name' => 'Embroidery', 'proficiency' => 'intermediate', 'years_of_experience' => 3, 'certification' => null],
            ],
            'jamil@bracmis.org' => [
                ['skill_name' => 'Driving', 'proficiency' => 'expert', 'years_of_experience' => 12, 'certification' => 'Professional Driving Licence'],
                ['skill_name' => 'Vehicle Maintenance', 'proficiency' => 'intermediate', 'years_of_experience' => 4, 'certification' => null],
            ],
            'nazma@bracmis.org' => [
                ['skill_name' => 'Plumbing', 'proficiency' => 'advanced', 'years_of_experience' => 5, 'certification' => null],
                ['skill_name' => 'Pipe Fitting', 'proficiency' => 'intermediate', 'years_of_experience' => 3, 'certification' => null],
            ],
            'mizanur@bracmis.org' => [
                ['skill_name' => 'Carpentry', 'proficiency' => 'advanced', 'years_of_experience' => 7, 'certification' => null],
                ['skill_name' => 'Furniture Finishing', 'proficiency' => 'intermediate', 'years_of_experience' => 4, 'certification' => null],
            ],
        ];

        // Profiles to publish on the public job board.
        $potentialCustomers = [
            'fatima@bracmis.org',
            'kabir@bracmis.org',
            'jamil@bracmis.org',
            'nazma@bracmis.org',
        ];

        $created = 0;

        foreach ($profiles as $email => $skills) {
            $user = User::where('email', $email)->first();

            if (! $user) {
                continue;
            }

            foreach ($skills as $skill) {
                $record = UserSkill::firstOrCreate(
                    ['user_id' => $user->id, 'skill_name' => $skill['skill_name']],
                    $skill
                );

                if ($record->wasRecentlyCreated) {
                    $created++;
                }
            }

            if (in_array($email, $potentialCustomers, true)) {
                $user->update(['profile_type' => User::PROFILE_POTENTIAL_CUSTOMER]);
            }
        }

        $this->command?->info("Created {$created} user skills.");
    }
}
