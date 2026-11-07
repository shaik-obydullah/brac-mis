<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Country;
use App\Models\User;
use App\Models\UserDocument;
use App\Models\UserMigrationHistory;
use App\Models\UserSkill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::create(['name' => 'manage users']);
        $role = Role::create(['name' => 'user-admin']);
        $role->givePermissionTo('manage users');

        $this->admin = User::factory()->create();
        $this->admin->assignRole($role);
    }

    public function test_users_list_is_accessible()
    {
        User::factory()->create(['name' => 'Listed User']);

        $this->actingAs($this->admin)
            ->get('/users')
            ->assertStatus(200)
            ->assertSee('Listed User');
    }

    public function test_user_can_be_created_without_assigning_roles()
    {
        $role = Role::create(['name' => 'field-agent']);

        $response = $this->actingAs($this->admin)->post('/users', [
            'name' => 'New User',
            'email' => 'new.user@example.com',
            'phone' => '01700000000',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
            'status' => 'active',
            'profile_type' => User::PROFILE_POTENTIAL_CUSTOMER,
            'roles' => [$role->name],
        ]);

        $user = User::where('email', 'new.user@example.com')->firstOrFail();

        $response->assertRedirect(route('users.show', $user));
        $this->assertTrue(Hash::check('secret-password', $user->password));
        $this->assertFalse($user->hasRole('field-agent'));
        $this->assertSame(User::PROFILE_POTENTIAL_CUSTOMER, $user->profile_type);
    }

    public function test_user_create_form_does_not_show_role_selection()
    {
        $this->actingAs($this->admin)
            ->get('/users/create')
            ->assertStatus(200)
            ->assertDontSee('name="roles[]"', false)
            ->assertDontSee('Roles');
    }

    public function test_user_created_without_profile_type_defaults_to_general_user()
    {
        $this->actingAs($this->admin)->post('/users', [
            'name' => 'Unclassified User',
            'email' => 'unclassified@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
            'status' => 'active',
        ]);

        $user = User::where('email', 'unclassified@example.com')->firstOrFail();

        $this->assertSame(User::PROFILE_GENERAL_USER, $user->profile_type);
        $this->assertSame('General User', $user->profile_type_label);
    }

    public function test_users_can_be_filtered_by_profile_type()
    {
        User::factory()->create(['name' => 'Reintegrated Person']);
        User::factory()->potentialCustomer()->create(['name' => 'Potential Person']);

        $this->actingAs($this->admin)
            ->get('/users?profile_type='.User::PROFILE_POTENTIAL_CUSTOMER)
            ->assertStatus(200)
            ->assertSee('Potential Person')
            ->assertDontSee('Reintegrated Person');
    }

    public function test_user_can_be_updated_without_changing_password()
    {
        $user = User::factory()->create([
            'password' => 'original-password',
            'status' => 'active',
        ]);
        $branch = Branch::factory()->create();

        $response = $this->actingAs($this->admin)->put("/users/{$user->id}", [
            'name' => 'Updated Name',
            'email' => $user->email,
            'phone' => '01800000000',
            'password' => '',
            'status' => 'inactive',
            'profile_type' => User::PROFILE_POTENTIAL_CUSTOMER,
            'branch_id' => $branch->id,
        ]);

        $user->refresh();

        $response->assertRedirect(route('users.show', $user));
        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('inactive', $user->status);
        $this->assertSame(User::PROFILE_POTENTIAL_CUSTOMER, $user->profile_type);
        $this->assertSame($branch->id, $user->branch_id);
        $this->assertTrue(Hash::check('original-password', $user->password));
    }

    public function test_user_can_be_deleted()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/users/{$user->id}");

        $response->assertRedirect('/users');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_user_cannot_delete_their_own_account()
    {
        $response = $this->actingAs($this->admin)->delete("/users/{$this->admin->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_user_profile_shows_skills_migration_and_document_sections()
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->get("/users/{$user->id}")
            ->assertStatus(200)
            ->assertSee('Add Skill')
            ->assertSee('Add Migration History')
            ->assertSee('Upload Document');
    }

    public function test_skill_can_be_added_and_removed()
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)->post("/users/{$user->id}/skills", [
            'skill_name' => 'Electrician',
            'proficiency' => 'advanced',
            'years_of_experience' => 5,
            'certification' => 'Trade Certificate',
        ])->assertRedirect(route('users.show', $user));

        $skill = UserSkill::where('user_id', $user->id)->firstOrFail();
        $this->assertSame('Electrician', $skill->skill_name);

        $this->actingAs($this->admin)
            ->delete("/users/{$user->id}/skills/{$skill->id}")
            ->assertRedirect(route('users.show', $user));

        $this->assertDatabaseMissing('user_skills', ['id' => $skill->id]);
    }

    public function test_migration_history_can_be_added_and_removed()
    {
        $user = User::factory()->create();
        $country = Country::factory()->create();

        $this->actingAs($this->admin)->post("/users/{$user->id}/migration-histories", [
            'country_id' => $country->id,
            'employer_name' => 'Gulf Contracting',
            'job_title' => 'Welder',
            'visa_type' => 'Work Visa',
            'start_date' => '2020-01-01',
            'end_date' => '2022-01-01',
            'status' => 'completed',
            'notes' => 'Returned after contract.',
        ])->assertRedirect(route('users.show', $user));

        $history = UserMigrationHistory::where('user_id', $user->id)->firstOrFail();
        $this->assertSame($country->id, $history->country_id);

        $this->actingAs($this->admin)
            ->delete("/users/{$user->id}/migration-histories/{$history->id}")
            ->assertRedirect(route('users.show', $user));

        $this->assertDatabaseMissing('user_migration_histories', ['id' => $history->id]);
    }

    public function test_document_can_be_uploaded_and_removed()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($this->admin)->post("/users/{$user->id}/documents", [
            'type' => 'Passport',
            'expiry_date' => '2030-01-01',
            'file' => UploadedFile::fake()->create('passport.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('users.show', $user));

        $document = UserDocument::where('user_id', $user->id)->firstOrFail();
        Storage::disk('public')->assertExists($document->file_path);

        $this->actingAs($this->admin)
            ->delete("/users/{$user->id}/documents/{$document->id}")
            ->assertRedirect(route('users.show', $user));

        $this->assertDatabaseMissing('user_documents', ['id' => $document->id]);
    }

    public function test_user_relations_are_removed_when_user_is_deleted()
    {
        $user = User::factory()->create();
        $user->skills()->create(['skill_name' => 'Mason', 'proficiency' => 'beginner']);
        $user->migrationHistories()->create(['start_date' => '2021-01-01', 'status' => 'completed']);
        $user->documents()->create(['type' => 'NID', 'file_path' => 'documents/users/nid.pdf']);

        $user->delete();

        $this->assertDatabaseCount('user_skills', 0);
        $this->assertDatabaseCount('user_migration_histories', 0);
        $this->assertDatabaseCount('user_documents', 0);
    }

    public function test_user_module_requires_manage_users_permission()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/users')->assertStatus(403);
    }

    public function test_non_super_admin_cannot_assign_super_admin_role()
    {
        $superAdminRole = Role::create(['name' => 'super-admin']);
        $superAdminRole->syncPermissions(Permission::all());

        $target = User::factory()->create();

        $this->actingAs($this->admin)->put("/users/{$target->id}", [
            'name' => $target->name,
            'email' => $target->email,
            'status' => 'active',
            'profile_type' => User::PROFILE_GENERAL_USER,
            'roles' => ['super-admin'],
        ])->assertSessionHas('error');

        $this->assertFalse($target->fresh()->hasRole('super-admin'));
    }

    public function test_non_super_admin_cannot_modify_a_super_admin_account()
    {
        $superAdminRole = Role::create(['name' => 'super-admin']);
        $superAdminRole->syncPermissions(Permission::all());

        $target = User::factory()->create(['name' => 'Protected Admin']);
        $target->assignRole($superAdminRole);

        $this->actingAs($this->admin)->put("/users/{$target->id}", [
            'name' => 'Hijacked Account',
            'email' => $target->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
            'status' => 'active',
            'profile_type' => User::PROFILE_GENERAL_USER,
            'roles' => ['super-admin'],
        ])->assertSessionHas('error');

        $this->assertSame('Protected Admin', $target->fresh()->name);
        $this->assertTrue($target->fresh()->hasRole('super-admin'));
    }

    public function test_non_super_admin_role_list_hides_super_admin()
    {
        Role::create(['name' => 'super-admin']);
        $target = User::factory()->create();

        $this->actingAs($this->admin)
            ->get("/users/{$target->id}/edit")
            ->assertOk()
            ->assertDontSee('value="super-admin"', false);
    }

    public function test_super_admin_can_assign_super_admin_role()
    {
        $superAdminRole = Role::create(['name' => 'super-admin']);
        $superAdminRole->syncPermissions(Permission::all());

        $actor = User::factory()->create();
        $actor->assignRole($superAdminRole);

        $target = User::factory()->create();

        $this->actingAs($actor)->put("/users/{$target->id}", [
            'name' => $target->name,
            'email' => $target->email,
            'status' => 'active',
            'profile_type' => User::PROFILE_GENERAL_USER,
            'roles' => ['super-admin'],
        ])->assertRedirect(route('users.show', $target));

        $this->assertTrue($target->fresh()->hasRole('super-admin'));
    }
}
