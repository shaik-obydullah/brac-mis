<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\PermissionCatalog;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsRoleManager(): User
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $this->actingAs($user);

        return $user;
    }

    public function test_roles_index_shows_roles_and_permission_counts()
    {
        $this->actingAsRoleManager();

        $response = $this->get('/roles');

        $response->assertOk()
            ->assertSeeText('Roles & Permissions')
            ->assertSeeText('super-admin')
            ->assertSeeText('admin')
            ->assertSeeText('manager')
            ->assertSeeText('field-officer')
            ->assertSeeText('viewer')
            ->assertSeeText('permission');
    }

    public function test_role_edit_page_lists_every_active_permission_grouped_by_module()
    {
        $this->actingAsRoleManager();

        $role = Role::findByName('admin');

        $response = $this->get('/roles/'.$role->id.'/edit');

        $response->assertOk();

        foreach (PermissionCatalog::groups() as $group) {
            $response->assertSeeText($group['label']);

            foreach ($group['permissions'] as $permission) {
                $response->assertSeeText($permission['name']);
            }
        }

        $html = $response->getContent();
        $this->assertSame(PermissionCatalog::total(), substr_count($html, 'name="permissions[]"'));

        $response->assertSeeText('View')
            ->assertSeeText('Create')
            ->assertSeeText('Edit')
            ->assertSeeText('Delete')
            ->assertSeeText('Manage');
    }

    public function test_retired_module_permissions_are_not_shown_on_role_edit_page()
    {
        $this->actingAsRoleManager();

        Permission::create(['name' => 'view beneficiaries']);
        Permission::create(['name' => 'create migrants']);
        Permission::create(['name' => 'edit returnees']);

        $role = Role::findByName('admin');

        $response = $this->get('/roles/'.$role->id.'/edit');

        $response->assertOk()
            ->assertDontSeeText('view beneficiaries')
            ->assertDontSeeText('create migrants')
            ->assertDontSeeText('edit returnees');

        $this->assertSame(
            PermissionCatalog::total(),
            substr_count($response->getContent(), 'name="permissions[]"')
        );
    }

    public function test_role_can_be_created_with_normalised_name_and_permissions()
    {
        $this->actingAsRoleManager();

        $response = $this->post('/roles', [
            'name' => 'Data Entry Officer',
            'permissions' => ['view courses', 'view reports'],
        ]);

        $response->assertRedirect('/roles');

        $this->assertDatabaseHas('roles', ['name' => 'data-entry-officer']);

        $role = Role::findByName('data-entry-officer');
        $this->assertTrue($role->hasPermissionTo('view courses'));
        $this->assertTrue($role->hasPermissionTo('view reports'));
    }

    public function test_role_name_must_be_unique_after_normalisation()
    {
        $this->actingAsRoleManager();

        $response = $this->post('/roles', [
            'name' => 'Admin',
            'permissions' => [],
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_role_can_be_updated_and_permissions_are_synced()
    {
        $this->actingAsRoleManager();

        $role = Role::findByName('admin');

        $response = $this->put('/roles/'.$role->id, [
            'name' => 'Operations Admin',
            'permissions' => ['view courses', 'view reports'],
        ]);

        $response->assertRedirect('/roles');

        $role->refresh();

        $this->assertSame('operations-admin', $role->name);
        $this->assertTrue($role->hasPermissionTo('view courses'));
        $this->assertTrue($role->hasPermissionTo('view reports'));
        $this->assertFalse($role->hasPermissionTo('view branches'));
    }

    public function test_deactivated_permissions_are_ignored_when_updating_a_role()
    {
        $this->actingAsRoleManager();

        Permission::create(['name' => 'view beneficiaries']);

        $role = Role::findByName('admin');

        $response = $this->put('/roles/'.$role->id, [
            'name' => 'admin',
            'permissions' => ['view courses', 'view beneficiaries'],
        ]);

        $response->assertRedirect('/roles');

        $role->refresh();

        $this->assertTrue($role->hasPermissionTo('view courses'));
        $this->assertFalse($role->hasPermissionTo('view beneficiaries'));
    }

    public function test_super_admin_role_cannot_be_edited()
    {
        $this->actingAsRoleManager();

        $superAdmin = Role::findByName('super-admin');

        $this->get('/roles/'.$superAdmin->id.'/edit')
            ->assertRedirect('/roles')
            ->assertSessionHas('error');

        $this->from('/roles')
            ->put('/roles/'.$superAdmin->id, [
                'name' => 'changed-super-admin',
                'permissions' => [],
            ])
            ->assertRedirect('/roles')
            ->assertSessionHas('error');

        $this->assertSame('super-admin', $superAdmin->fresh()->name);
    }

    public function test_user_roles_page_is_accessible()
    {
        $this->actingAsRoleManager();

        $user = User::factory()->create(['name' => 'Role Assign Target']);
        $user->assignRole('viewer');

        $this->get('/roles/users')
            ->assertOk()
            ->assertSeeText('Role Assign Target')
            ->assertSeeText('viewer');
    }
}
