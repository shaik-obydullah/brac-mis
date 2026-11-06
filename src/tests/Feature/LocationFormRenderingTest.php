<?php

namespace Tests\Feature;

use App\Models\Beneficiary;
use App\Models\Branch;
use App\Models\Migrant;
use App\Models\Returnee;
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
            'view beneficiaries', 'create beneficiaries', 'edit beneficiaries',
            'view migrants', 'create migrants', 'edit migrants',
            'view returnees', 'create returnees', 'edit returnees',
            'view branches', 'create branches', 'edit branches',
        ] as $permission) {
            Permission::create(['name' => $permission])->assignRole($role);
        }

        $this->user = User::factory()->create();
        $this->user->assignRole($role);
    }

    public function test_beneficiary_forms_render_location_dropdowns()
    {
        $beneficiary = Beneficiary::factory()->create();

        $this->actingAs($this->user)->get('/beneficiaries/create')
            ->assertStatus(200)
            ->assertSee('name="division_id"', false)
            ->assertSee('name="district_id"', false)
            ->assertSee('name="upazila_id"', false);

        $this->actingAs($this->user)->get("/beneficiaries/{$beneficiary->id}/edit")
            ->assertStatus(200)
            ->assertSee('name="division_id"', false);
    }

    public function test_migrant_forms_render_origin_location_dropdowns()
    {
        $migrant = Migrant::factory()->create();

        $this->actingAs($this->user)->get('/migrants/create')
            ->assertStatus(200)
            ->assertSee('name="origin_division_id"', false)
            ->assertSee('name="origin_district_id"', false)
            ->assertSee('name="origin_upazila_id"', false);

        $this->actingAs($this->user)->get("/migrants/{$migrant->id}/edit")
            ->assertStatus(200)
            ->assertSee('name="origin_division_id"', false);
    }

    public function test_returnee_forms_render_location_dropdowns()
    {
        $returnee = Returnee::factory()->create();

        $this->actingAs($this->user)->get('/returnees/create')
            ->assertStatus(200)
            ->assertSee('name="division_id"', false)
            ->assertSee('name="district_id"', false)
            ->assertSee('name="upazila_id"', false);

        $this->actingAs($this->user)->get("/returnees/{$returnee->id}/edit")
            ->assertStatus(200)
            ->assertSee('name="division_id"', false);
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
