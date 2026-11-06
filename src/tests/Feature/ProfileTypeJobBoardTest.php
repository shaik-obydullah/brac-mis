<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProfileTypeJobBoardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::create(['name' => 'manage users']);
        $role = Role::create(['name' => 'user-admin']);
        $role->givePermissionTo('manage users');

        $this->admin = User::factory()->create();
        $this->admin->assignRole($role);
    }

    public function test_skill_access_page_shows_the_potential_customer_checkbox()
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->get('/skill-access?user_id='.$user->id)
            ->assertStatus(200)
            ->assertSee('Mark as potential customer');
    }

    public function test_a_profile_can_be_marked_as_a_potential_customer()
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->post("/skill-access/{$user->id}/profile-type", [
                'profile_type' => User::PROFILE_POTENTIAL_CUSTOMER,
            ])
            ->assertRedirect(route('skill-access.index', ['user_id' => $user->id]));

        $this->assertSame(User::PROFILE_POTENTIAL_CUSTOMER, $user->fresh()->profile_type);
    }

    public function test_a_potential_customer_can_be_reverted_to_a_reintegrated_profile()
    {
        $user = User::factory()->potentialCustomer()->create();

        $this->actingAs($this->admin)
            ->post("/skill-access/{$user->id}/profile-type", [
                'profile_type' => User::PROFILE_REINTEGRATED,
            ])
            ->assertRedirect(route('skill-access.index', ['user_id' => $user->id]));

        $this->assertSame(User::PROFILE_REINTEGRATED, $user->fresh()->profile_type);
    }

    public function test_only_potential_customers_are_listed_on_the_public_job_board()
    {
        $branch = Branch::factory()->create();

        User::factory()->create([
            'name' => 'Reintegrated Person',
            'branch_id' => $branch->id,
        ]);

        User::factory()->potentialCustomer()->create([
            'name' => 'Potential Person',
            'branch_id' => $branch->id,
        ]);

        $this->get('/job-board')
            ->assertStatus(200)
            ->assertSee('Potential Person')
            ->assertDontSee('Reintegrated Person');
    }

    public function test_a_reintegrated_profile_cannot_be_viewed_on_the_public_job_board()
    {
        $user = User::factory()->create();

        $this->get("/job-board/{$user->id}")->assertNotFound();
    }

    public function test_a_potential_customer_profile_is_publicly_viewable()
    {
        $user = User::factory()->potentialCustomer()->create();

        $this->get("/job-board/{$user->id}")
            ->assertStatus(200)
            ->assertSee($user->name)
            ->assertSee('Potential Customer');
    }
}
