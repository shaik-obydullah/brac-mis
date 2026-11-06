<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LocationFormRenderingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'location-tester']);
        foreach ([
            'view branches', 'create branches', 'edit branches',
        ] as $permission) {
            Permission::create(['name' => $permission])->assignRole($role);
        }

        $this->user = User::factory()->create();
        $this->user->assignRole($role);
    }

    public function test_branch_forms_render_location_dropdowns()
    {
        $branch = Branch::factory()->create();

        $this->actingAs($this->user)->get('/branches/create')
            ->assertStatus(200)
            ->assertSee('name="division"', false)
            ->assertSee('name="district"', false)
            ->assertSee('name="upazila"', false);

        $this->actingAs($this->user)->get("/branches/{$branch->id}/edit")
            ->assertStatus(200)
            ->assertSee('name="upazila"', false);
    }
}
