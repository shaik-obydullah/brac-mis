<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Database\Seeders\BranchSeeder;
use Database\Seeders\SampleUpazilaBranchSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BranchHierarchyTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Branch $headOffice;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'hrm-admin']);
        foreach (['view branches', 'create branches', 'edit branches', 'delete branches'] as $permission) {
            Permission::create(['name' => $permission])->assignRole($role);
        }

        $this->user = User::factory()->create();
        $this->user->assignRole($role);

        $this->headOffice = Branch::where('type', Branch::TYPE_HEAD_OFFICE)->first()
            ?? Branch::factory()->create([
                'name' => 'Head Office',
                'code' => 'HO-001',
                'type' => Branch::TYPE_HEAD_OFFICE,
                'parent_id' => null,
            ]);
    }

    public function test_branch_list_is_accessible()
    {
        $this->actingAs($this->user)->get('/branches')->assertStatus(200);
    }

    public function test_sample_upazila_branches_are_seeded_under_district_branches()
    {
        $this->seed([BranchSeeder::class, SampleUpazilaBranchSeeder::class]);

        $this->assertSame(15, Branch::where('type', Branch::TYPE_UPAZILA)->count());
        $this->assertSame(5, Branch::where('type', Branch::TYPE_DISTRICT)->count());
        $this->assertSame(
            0,
            Branch::where('type', Branch::TYPE_UPAZILA)
                ->whereHas('parent', fn ($query) => $query->where('type', '!=', Branch::TYPE_DISTRICT))
                ->count()
        );

        $savar = Branch::where('upazila', 'Savar')->firstOrFail();

        $this->assertSame('Savar Upazila Branch', $savar->name);
        $this->assertSame('Dhaka', $savar->district);
        $this->assertSame('Dhaka', $savar->division);
        $this->assertSame('Dhaka Main Branch', $savar->parent->name);
    }

    public function test_district_can_be_created_under_head_office()
    {
        $response = $this->actingAs($this->user)->post('/branches', [
            'name' => 'Dhaka District',
            'code' => 'DIS-001',
            'type' => Branch::TYPE_DISTRICT,
            'parent_id' => $this->headOffice->id,
            'district' => 'Dhaka',
            'division' => 'Dhaka',
            'status' => 1,
        ]);

        $response->assertRedirect('/branches');
        $this->assertDatabaseHas('branches', [
            'name' => 'Dhaka District',
            'type' => Branch::TYPE_DISTRICT,
            'parent_id' => $this->headOffice->id,
        ]);
    }

    public function test_upazila_can_be_created_under_district()
    {
        $district = Branch::factory()->create([
            'type' => Branch::TYPE_DISTRICT,
            'parent_id' => $this->headOffice->id,
        ]);

        $response = $this->actingAs($this->user)->post('/branches', [
            'name' => 'Savar Upazila',
            'code' => 'UPA-001',
            'type' => Branch::TYPE_UPAZILA,
            'parent_id' => $district->id,
            'district' => 'Dhaka',
            'division' => 'Dhaka',
            'status' => 1,
        ]);

        $response->assertRedirect('/branches');
        $this->assertDatabaseHas('branches', [
            'name' => 'Savar Upazila',
            'type' => Branch::TYPE_UPAZILA,
            'parent_id' => $district->id,
        ]);
    }

    public function test_district_cannot_be_created_under_another_district()
    {
        $district = Branch::factory()->create([
            'type' => Branch::TYPE_DISTRICT,
            'parent_id' => $this->headOffice->id,
        ]);

        $response = $this->actingAs($this->user)->post('/branches', [
            'name' => 'Invalid District',
            'code' => 'DIS-002',
            'type' => Branch::TYPE_DISTRICT,
            'parent_id' => $district->id,
            'district' => 'Dhaka',
            'division' => 'Dhaka',
            'status' => 1,
        ]);

        $response->assertSessionHasErrors('parent_id');
        $this->assertDatabaseMissing('branches', ['code' => 'DIS-002']);
    }

    public function test_second_head_office_cannot_be_created()
    {
        $response = $this->actingAs($this->user)->post('/branches', [
            'name' => 'Second Head Office',
            'code' => 'HO-002',
            'type' => Branch::TYPE_HEAD_OFFICE,
            'district' => 'Dhaka',
            'division' => 'Dhaka',
            'status' => 1,
        ]);

        $response->assertSessionHasErrors('type');
        $this->assertDatabaseMissing('branches', ['code' => 'HO-002']);
    }

    public function test_branch_with_children_cannot_be_deleted()
    {
        $district = Branch::factory()->create([
            'type' => Branch::TYPE_DISTRICT,
            'parent_id' => $this->headOffice->id,
        ]);

        Branch::factory()->create([
            'type' => Branch::TYPE_UPAZILA,
            'parent_id' => $district->id,
        ]);

        $response = $this->actingAs($this->user)->delete("/branches/{$district->id}");

        $response->assertRedirect('/branches');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('branches', ['id' => $district->id]);
    }

    public function test_branch_type_cannot_change_while_it_has_children()
    {
        $district = Branch::factory()->create([
            'type' => Branch::TYPE_DISTRICT,
            'parent_id' => $this->headOffice->id,
        ]);

        Branch::factory()->create([
            'type' => Branch::TYPE_UPAZILA,
            'parent_id' => $district->id,
        ]);

        $response = $this->actingAs($this->user)->put("/branches/{$district->id}", [
            'name' => $district->name,
            'code' => $district->code,
            'type' => Branch::TYPE_UPAZILA,
            'parent_id' => $this->headOffice->id,
            'status' => 1,
        ]);

        $response->assertSessionHasErrors('type');
        $this->assertDatabaseHas('branches', [
            'id' => $district->id,
            'type' => Branch::TYPE_DISTRICT,
        ]);
    }
}
