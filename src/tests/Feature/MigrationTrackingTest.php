<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Migrant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MigrationTrackingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Country $country;

    protected function setUp(): void
    {
        parent::setUp();
        $role = Role::create(['name' => 'test-role']);
        foreach (['view migrants', 'create migrants', 'edit migrants', 'delete migrants'] as $p) {
            Permission::create(['name' => $p])->assignRole($role);
        }
        $this->user = User::factory()->create();
        $this->user->assignRole($role);
        $this->country = Country::factory()->create();
    }

    public function test_migrants_list_is_accessible()
    {
        Migrant::factory()->count(3)->create();
        $response = $this->actingAs($this->user)->get('/migrants');
        $response->assertStatus(200);
    }

    public function test_migrant_can_be_created()
    {
        $response = $this->actingAs($this->user)->post('/migrants', [
            'name' => 'Jane Doe',
            'gender' => 'female',
            'status' => 'registered',
        ]);

        $response->assertRedirect('/migrants');
        $this->assertDatabaseHas('migrants', ['name' => 'Jane Doe']);
    }

    public function test_migrant_status_transitions()
    {
        $migrant = Migrant::factory()->create(['status' => 'registered']);

        $response = $this->actingAs($this->user)->put("/migrants/{$migrant->id}", [
            'name' => $migrant->name,
            'status' => 'pre_departure',
        ]);

        $response->assertRedirect('/migrants');
        $this->assertDatabaseHas('migrants', ['id' => $migrant->id, 'status' => 'pre_departure']);
    }

    public function test_api_returns_migrants()
    {
        Migrant::factory()->count(3)->create();
        $response = $this->actingAs($this->user)->getJson('/api/migrants');
        $response->assertStatus(200)->assertJsonCount(3, 'data');
    }
}
