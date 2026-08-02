<?php

namespace Tests\Feature;

use App\Models\Beneficiary;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BeneficiaryManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();
        $role = Role::create(['name' => 'test-role']);
        foreach (['view beneficiaries', 'create beneficiaries', 'edit beneficiaries', 'delete beneficiaries'] as $p) {
            Permission::create(['name' => $p])->assignRole($role);
        }
        $this->user = User::factory()->create();
        $this->user->assignRole($role);
        $this->branch = Branch::factory()->create();
    }

    public function test_beneficiaries_list_is_accessible()
    {
        Beneficiary::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->get('/beneficiaries');
        $response->assertStatus(200);
    }

    public function test_beneficiary_creation_form_is_accessible()
    {
        $response = $this->actingAs($this->user)->get('/beneficiaries/create');
        $response->assertStatus(200);
    }

    public function test_beneficiary_can_be_created()
    {
        $response = $this->actingAs($this->user)->post('/beneficiaries', [
            'name' => 'John Doe',
            'gender' => 'male',
            'status' => 'active',
        ]);

        $response->assertRedirect('/beneficiaries');
        $this->assertDatabaseHas('beneficiaries', ['name' => 'John Doe']);
    }

    public function test_beneficiary_can_be_viewed()
    {
        $beneficiary = Beneficiary::factory()->create();
        $response = $this->actingAs($this->user)->get("/beneficiaries/{$beneficiary->id}");
        $response->assertStatus(200);
    }

    public function test_beneficiary_can_be_updated()
    {
        $beneficiary = Beneficiary::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->user)->put("/beneficiaries/{$beneficiary->id}", [
            'name' => 'New Name',
            'branch_id' => $this->branch->id,
        ]);

        $response->assertRedirect('/beneficiaries');
        $this->assertDatabaseHas('beneficiaries', ['name' => 'New Name']);
    }

    public function test_beneficiary_can_be_deleted()
    {
        $beneficiary = Beneficiary::factory()->create();

        $response = $this->actingAs($this->user)->delete("/beneficiaries/{$beneficiary->id}");
        $response->assertRedirect('/beneficiaries');
        $this->assertDatabaseMissing('beneficiaries', ['id' => $beneficiary->id]);
    }

    public function test_api_returns_beneficiaries()
    {
        Beneficiary::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->getJson('/api/beneficiaries');
        $response->assertStatus(200)->assertJsonCount(3, 'data');
    }
}
