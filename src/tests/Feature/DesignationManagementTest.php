<?php

namespace Tests\Feature;

use App\Models\Designation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesignationManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'hrm-admin']);
        foreach (['view staff', 'view designations', 'create designations', 'edit designations', 'delete designations'] as $permission) {
            Permission::create(['name' => $permission])->assignRole($role);
        }

        $this->user = User::factory()->create();
        $this->user->assignRole($role);
    }

    public function test_hrm_overview_is_accessible()
    {
        $this->actingAs($this->user)->get('/hrm')->assertStatus(200);
    }

    public function test_designations_list_is_accessible()
    {
        Designation::create(['name' => 'Field Officer', 'status' => true]);

        $this->actingAs($this->user)->get('/designations')->assertStatus(200);
    }

    public function test_designation_can_be_created()
    {
        $response = $this->actingAs($this->user)->post('/designations', [
            'name' => 'Program Manager',
            'status' => 1,
        ]);

        $response->assertRedirect('/designations');
        $this->assertDatabaseHas('designations', ['name' => 'Program Manager']);
    }

    public function test_designation_can_be_updated()
    {
        $designation = Designation::create(['name' => 'Old Title', 'status' => true]);

        $response = $this->actingAs($this->user)->put("/designations/{$designation->id}", [
            'name' => 'New Title',
            'status' => 0,
        ]);

        $response->assertRedirect('/designations');
        $this->assertDatabaseHas('designations', [
            'id' => $designation->id,
            'name' => 'New Title',
            'status' => 0,
        ]);
    }

    public function test_designation_can_be_deleted()
    {
        $designation = Designation::create(['name' => 'Temporary Title', 'status' => true]);

        $response = $this->actingAs($this->user)->delete("/designations/{$designation->id}");

        $response->assertRedirect('/designations');
        $this->assertDatabaseMissing('designations', ['id' => $designation->id]);
    }
}
