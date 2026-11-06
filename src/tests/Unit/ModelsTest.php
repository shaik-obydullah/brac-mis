<?php

namespace Tests\Unit;

use App\Models\Branch;
use App\Models\Country;
use App\Models\Staff;
use App\Models\User;
use App\Models\UserMigrationHistory;
use App\Models\UserSkill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_branch_has_users()
    {
        $branch = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $branch->id]);

        $this->assertTrue($branch->users->contains($user));
    }

    public function test_user_has_skills()
    {
        $user = User::factory()->create();
        $skill = UserSkill::create([
            'user_id' => $user->id,
            'skill_name' => 'Tailoring',
            'proficiency' => 'advanced',
            'years_of_experience' => 4,
        ]);

        $this->assertTrue($user->skills->contains($skill));
        $this->assertTrue($skill->user->is($user));
    }

    public function test_user_has_migration_histories()
    {
        $user = User::factory()->create();
        $country = Country::factory()->create();
        $history = UserMigrationHistory::create([
            'user_id' => $user->id,
            'country_id' => $country->id,
            'employer_name' => 'Gulf Contracting',
            'job_title' => 'Electrician',
            'status' => 'returned',
            'start_date' => '2022-01-01',
            'end_date' => '2025-06-30',
        ]);

        $this->assertTrue($user->migrationHistories->contains($history));
        $this->assertTrue($history->user->is($user));
        $this->assertTrue($history->country->is($country));
    }

    public function test_country_has_migration_histories()
    {
        $country = Country::factory()->create();
        $history = UserMigrationHistory::create([
            'user_id' => User::factory()->create()->id,
            'country_id' => $country->id,
            'status' => 'returned',
            'start_date' => '2022-01-01',
        ]);

        $this->assertTrue($country->migrationHistories->contains($history));
    }

    public function test_profile_type_scopes_and_label()
    {
        $reintegrated = User::factory()->create();
        $potential = User::factory()->potentialCustomer()->create();

        $this->assertTrue(User::reintegrated()->get()->contains($reintegrated));
        $this->assertFalse(User::reintegrated()->get()->contains($potential));
        $this->assertTrue(User::potentialCustomers()->get()->contains($potential));
        $this->assertFalse(User::potentialCustomers()->get()->contains($reintegrated));

        $this->assertTrue($potential->isPotentialCustomer());
        $this->assertSame('Potential Customer Profile', $potential->profile_type_label);
        $this->assertSame('Reintegrated Profile', $reintegrated->profile_type_label);
    }

    public function test_user_has_staff()
    {
        $staff = Staff::factory()->create();

        $this->assertNotNull($staff->user);
        $this->assertNotNull($staff->branch);
        $this->assertTrue($staff->user->staff->is($staff));
    }
}
