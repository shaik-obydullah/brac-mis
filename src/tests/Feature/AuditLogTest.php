<?php

namespace Tests\Feature;

use App\Models\Branch;
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
        $perm = Permission::create(['name' => 'create branches']);
        $role->givePermissionTo($perm);
        $user = User::factory()->create();
        $user->assignRole($role);

        $headOffice = Branch::where('type', Branch::TYPE_HEAD_OFFICE)->firstOrFail();

        $response = $this->actingAs($user)->post('/branches', [
            'name' => 'Audit Test Branch',
            'code' => 'AUD-001',
            'type' => Branch::TYPE_DISTRICT,
            'parent_id' => $headOffice->id,
            'division' => 'Dhaka',
            'district' => 'Dhaka',
            'status' => '1',
        ]);

        $response->assertRedirect('/branches');

        $this->assertDatabaseHas('branches', ['name' => 'Audit Test Branch']);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'created',
            'user_id' => $user->id,
        ]);
    }
}
