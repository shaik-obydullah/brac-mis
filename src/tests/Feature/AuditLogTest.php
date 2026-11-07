<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    private function auditUser(): User
    {
        $role = Role::create(['name' => 'audit-viewer']);
        $role->givePermissionTo(Permission::create(['name' => 'view audit logs']));
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

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

    public function test_audit_log_page_uses_readable_module_and_action_names()
    {
        $user = $this->auditUser();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'created',
            'subject_type' => 'App\Models\StaffIncentive',
            'subject_id' => 10,
            'new_values' => ['title' => 'Field travel allowance'],
            'ip_address' => '127.0.0.1',
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'follow_up_scheduled',
            'subject_type' => 'App\Models\CourseEnrollment',
            'subject_id' => 20,
            'ip_address' => '127.0.0.1',
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'updated',
            'subject_type' => 'App\Models\Beneficiary',
            'subject_id' => 30,
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($user)->get('/audit-logs');

        $response->assertOk()
            ->assertSeeText('Staff Incentive')
            ->assertSeeText('Course Enrollment')
            ->assertSeeText('Follow-up scheduled')
            ->assertSeeText('Legacy Profile')
            ->assertSeeText('Field travel allowance')
            ->assertDontSeeText('StaffIncentive')
            ->assertDontSeeText('CourseEnrollment')
            ->assertDontSeeText('Beneficiary')
            ->assertDontSeeText('follow_up_scheduled');
    }

    public function test_audit_log_page_can_be_filtered_by_module_and_action()
    {
        $user = $this->auditUser();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'created',
            'subject_type' => 'App\Models\Course',
            'subject_id' => 1,
            'new_values' => ['title' => 'Tailoring'],
            'ip_address' => '127.0.0.1',
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'deleted',
            'subject_type' => 'App\Models\Staff',
            'subject_id' => 2,
            'old_values' => ['name' => 'Removed Staff'],
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($user)->get('/audit-logs?module=' . urlencode('App\Models\Course'));

        $response->assertOk()
            ->assertSeeText('Tailoring')
            ->assertDontSeeText('Removed Staff');

        $response = $this->actingAs($user)->get('/audit-logs?action=deleted');

        $response->assertOk()
            ->assertSeeText('Removed Staff')
            ->assertDontSeeText('Tailoring');
    }
}
