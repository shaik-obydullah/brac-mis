<?php

namespace Tests\Feature;

use App\Models\Migrant;
use App\Models\Returnee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReintegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $role = Role::create(['name' => 'test-role']);
        foreach (['view returnees', 'create returnees', 'edit returnees'] as $p) {
            Permission::create(['name' => $p])->assignRole($role);
        }
        $this->user = User::factory()->create();
        $this->user->assignRole($role);
    }

    public function test_returnees_list_is_accessible()
    {
        Returnee::factory()->count(3)->create();
        $response = $this->actingAs($this->user)->get('/returnees');
        $response->assertStatus(200);
    }

    public function test_returnee_can_be_created()
    {
        $migrant = Migrant::factory()->create();
        $response = $this->actingAs($this->user)->post('/returnees', [
            'migrant_id' => $migrant->id,
            'beneficiary_id' => $migrant->beneficiary_id,
            'return_date' => now()->subDays(30)->format('Y-m-d'),
            'return_reason' => 'Contract completed',
            'current_status' => 'assessed',
        ]);

        $response->assertRedirect('/returnees');
        $this->assertDatabaseHas('returnees', ['return_reason' => 'Contract completed']);
    }
}
