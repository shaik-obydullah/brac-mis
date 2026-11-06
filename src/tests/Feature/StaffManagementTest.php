<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StaffManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'staff-admin']);
        foreach (['view staff', 'create staff', 'edit staff'] as $permission) {
            Permission::create(['name' => $permission])->assignRole($role);
        }

        $this->admin = User::factory()->create();
        $this->admin->assignRole($role);
    }

    public function test_staff_create_form_shows_role_selection()
    {
        $this->actingAs($this->admin)
            ->get('/staff/create')
            ->assertStatus(200)
            ->assertSee('name="roles[]"', false)
            ->assertSee('Roles')
            ->assertSee('Staff Admin');
    }

    public function test_staff_can_be_created_with_roles_assigned_to_its_user()
    {
        $role = Role::create(['name' => 'field-agent']);
        $user = User::factory()->create();
        $branch = Branch::factory()->create();

        $response = $this->actingAs($this->admin)->post('/staff', [
            'user_id' => $user->id,
            'employee_id' => 'EMP-1001',
            'designation' => 'Field Officer',
            'branch_id' => $branch->id,
            'phone' => '01700000000',
            'roles' => [$role->name],
        ]);

        $response->assertRedirect('/staff');

        $staff = Staff::where('employee_id', 'EMP-1001')->firstOrFail();
        $this->assertSame($user->id, $staff->user_id);
        $this->assertTrue($user->fresh()->hasRole('field-agent'));
    }

    public function test_staff_roles_can_be_updated()
    {
        $role = Role::create(['name' => 'field-agent']);
        $user = User::factory()->create();
        $branch = Branch::factory()->create();
        $staff = Staff::factory()->create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
        ]);

        $this->actingAs($this->admin)->put("/staff/{$staff->id}", [
            'user_id' => $user->id,
            'employee_id' => $staff->employee_id,
            'designation' => $staff->designation,
            'branch_id' => $branch->id,
            'phone' => $staff->phone,
            'roles' => [$role->name],
        ])->assertRedirect('/staff');

        $this->assertTrue($user->fresh()->hasRole('field-agent'));
    }
}
