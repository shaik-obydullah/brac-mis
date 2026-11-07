<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ApiAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<int, string>  $permissions
     */
    private function userWith(array $permissions): User
    {
        $role = Role::create(['name' => 'api-role-'.uniqid()]);

        foreach ($permissions as $permission) {
            $role->givePermissionTo(Permission::firstOrCreate(['name' => $permission]));
        }

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_api_requires_permission_to_read_branches()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/branches')->assertForbidden();
    }

    public function test_api_allows_branch_reads_with_view_permission()
    {
        Branch::factory()->create(['name' => 'API Branch']);

        Sanctum::actingAs($this->userWith(['view branches']));

        $this->getJson('/api/branches')
            ->assertOk()
            ->assertJsonFragment(['name' => 'API Branch']);
    }

    public function test_api_branch_writes_require_their_own_permission()
    {
        Sanctum::actingAs($this->userWith(['view branches']));

        $this->postJson('/api/branches', [
            'name' => 'New Branch',
            'code' => 'API-1',
            'type' => 'district',
        ])->assertForbidden();
    }

    public function test_api_staff_reads_require_view_staff_permission()
    {
        Sanctum::actingAs($this->userWith(['view branches']));

        $this->getJson('/api/staff')->assertForbidden();
    }

    public function test_api_reports_require_view_reports_permission()
    {
        Sanctum::actingAs($this->userWith(['view branches']));

        $this->getJson('/api/reports/profile-summary')->assertForbidden();
    }

    public function test_api_report_exports_require_export_reports_permission()
    {
        Sanctum::actingAs($this->userWith(['view reports']));

        $this->getJson('/api/reports/export/profile-summary')->assertForbidden();
    }

    public function test_api_rejects_deactivated_accounts_with_existing_tokens()
    {
        $user = $this->userWith(['view branches']);
        Sanctum::actingAs($user);

        $user->update(['status' => 'inactive']);

        $this->getJson('/api/branches')->assertForbidden();
    }
}
