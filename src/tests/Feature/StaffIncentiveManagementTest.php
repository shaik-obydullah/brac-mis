<?php

namespace Tests\Feature;

use App\Models\Staff;
use App\Models\StaffIncentive;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StaffIncentiveManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Staff $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'hrm-admin']);
        foreach ([
            'view staff', 'view staff incentives', 'create staff incentives',
            'edit staff incentives', 'delete staff incentives',
        ] as $permission) {
            Permission::create(['name' => $permission])->assignRole($role);
        }

        $this->user = User::factory()->create();
        $this->user->assignRole($role);
        $this->staff = Staff::factory()->create();
    }

    public function test_incentives_list_is_accessible()
    {
        StaffIncentive::create([
            'staff_id' => $this->staff->id,
            'title' => 'Performance bonus',
            'amount' => 5000,
            'incentive_date' => now()->toDateString(),
            'status' => StaffIncentive::STATUS_PAID,
        ]);

        $this->actingAs($this->user)->get('/staff-incentives')->assertStatus(200);
    }

    public function test_incentive_can_be_created()
    {
        $response = $this->actingAs($this->user)->post('/staff-incentives', [
            'staff_id' => $this->staff->id,
            'title' => 'Festival bonus',
            'amount' => 7500,
            'incentive_date' => now()->toDateString(),
            'status' => StaffIncentive::STATUS_PENDING,
            'notes' => 'Pending approval.',
        ]);

        $response->assertRedirect('/staff-incentives');
        $this->assertDatabaseHas('staff_incentives', [
            'staff_id' => $this->staff->id,
            'title' => 'Festival bonus',
            'status' => StaffIncentive::STATUS_PENDING,
        ]);
    }

    public function test_incentive_can_be_updated()
    {
        $incentive = StaffIncentive::create([
            'staff_id' => $this->staff->id,
            'title' => 'Old Incentive',
            'amount' => 1000,
            'incentive_date' => now()->toDateString(),
            'status' => StaffIncentive::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->user)->put("/staff-incentives/{$incentive->id}", [
            'staff_id' => $this->staff->id,
            'title' => 'Updated Incentive',
            'amount' => 2000,
            'incentive_date' => now()->toDateString(),
            'status' => StaffIncentive::STATUS_PAID,
        ]);

        $response->assertRedirect('/staff-incentives');
        $this->assertDatabaseHas('staff_incentives', [
            'id' => $incentive->id,
            'title' => 'Updated Incentive',
            'amount' => 2000,
            'status' => StaffIncentive::STATUS_PAID,
        ]);
    }

    public function test_incentive_can_be_deleted()
    {
        $incentive = StaffIncentive::create([
            'staff_id' => $this->staff->id,
            'title' => 'Temporary Incentive',
            'amount' => 1000,
            'incentive_date' => now()->toDateString(),
            'status' => StaffIncentive::STATUS_PAID,
        ]);

        $response = $this->actingAs($this->user)->delete("/staff-incentives/{$incentive->id}");

        $response->assertRedirect('/staff-incentives');
        $this->assertDatabaseMissing('staff_incentives', ['id' => $incentive->id]);
    }

    public function test_staff_profile_lists_incentives()
    {
        StaffIncentive::create([
            'staff_id' => $this->staff->id,
            'title' => 'Profile Incentive',
            'amount' => 3000,
            'incentive_date' => now()->toDateString(),
            'status' => StaffIncentive::STATUS_PAID,
        ]);

        $this->actingAs($this->user)
            ->get("/staff/{$this->staff->id}")
            ->assertStatus(200)
            ->assertSee('Profile Incentive');
    }
}
