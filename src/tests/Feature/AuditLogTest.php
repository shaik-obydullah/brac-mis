<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_actions_are_logged()
    {
        $role = Role::create(['name' => 'test-role']);
        $perm = Permission::create(['name' => 'create beneficiaries']);
        $role->givePermissionTo($perm);
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->post('/beneficiaries', [
            'name' => 'Audit Test',
            'gender' => 'male',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('beneficiaries', ['name' => 'Audit Test']);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'created',
            'user_id' => $user->id,
        ]);
    }
}
