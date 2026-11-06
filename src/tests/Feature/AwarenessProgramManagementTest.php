<?php

namespace Tests\Feature;

use App\Models\AwarenessProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AwarenessProgramManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'branch-admin']);
        foreach ([
            'view awareness programs', 'create awareness programs',
            'edit awareness programs', 'delete awareness programs',
        ] as $permission) {
            Permission::create(['name' => $permission])->assignRole($role);
        }

        $this->user = User::factory()->create();
        $this->user->assignRole($role);
    }

    public function test_awareness_programs_list_is_accessible()
    {
        AwarenessProgram::create([
            'program_name' => 'Safe Migration Session',
            'division' => 'Dhaka',
            'district' => 'Dhaka',
            'upazila' => 'Savar',
        ]);

        $this->actingAs($this->user)->get('/awareness-programs')->assertStatus(200);
    }

    public function test_awareness_program_can_be_created()
    {
        $response = $this->actingAs($this->user)->post('/awareness-programs', [
            'program_name' => 'Community Awareness',
            'division' => 'Sylhet',
            'district' => 'Sylhet',
            'upazila' => 'Sylhet Sadar',
            'address' => 'Zindabazar, Sylhet',
        ]);

        $response->assertRedirect('/awareness-programs');
        $this->assertDatabaseHas('awareness_programs', [
            'program_name' => 'Community Awareness',
            'division' => 'Sylhet',
            'district' => 'Sylhet',
            'upazila' => 'Sylhet Sadar',
        ]);
    }

    public function test_awareness_program_can_be_updated()
    {
        $program = AwarenessProgram::create([
            'program_name' => 'Old Program',
            'division' => 'Dhaka',
            'district' => 'Dhaka',
            'upazila' => 'Savar',
        ]);

        $response = $this->actingAs($this->user)->put("/awareness-programs/{$program->id}", [
            'program_name' => 'Updated Program',
            'division' => 'Khulna',
            'district' => 'Khulna',
            'upazila' => 'Khalishpur',
            'address' => 'Khalishpur, Khulna',
        ]);

        $response->assertRedirect('/awareness-programs');
        $this->assertDatabaseHas('awareness_programs', [
            'id' => $program->id,
            'program_name' => 'Updated Program',
            'division' => 'Khulna',
            'district' => 'Khulna',
            'upazila' => 'Khalishpur',
        ]);
    }

    public function test_awareness_program_can_be_deleted()
    {
        $program = AwarenessProgram::create([
            'program_name' => 'Temporary Program',
            'division' => 'Dhaka',
            'district' => 'Dhaka',
            'upazila' => 'Savar',
        ]);

        $response = $this->actingAs($this->user)->delete("/awareness-programs/{$program->id}");

        $response->assertRedirect('/awareness-programs');
        $this->assertDatabaseMissing('awareness_programs', ['id' => $program->id]);
    }
}
