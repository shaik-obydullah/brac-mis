<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $role = Role::create(['name' => 'test-role']);
        Permission::create(['name' => 'view reports'])->assignRole($role);
        $this->user = User::factory()->create();
        $this->user->assignRole($role);
    }

    public function test_reports_page_is_accessible()
    {
        $response = $this->actingAs($this->user)->get('/reports');
        $response->assertStatus(200);
    }

    public function test_reports_page_shows_module_sections_and_recent_exports()
    {
        $this->user->givePermissionTo([
            Permission::firstOrCreate(['name' => 'view courses']),
            Permission::firstOrCreate(['name' => 'view certificates']),
            Permission::firstOrCreate(['name' => 'view staff incentives']),
            Permission::firstOrCreate(['name' => 'view finances']),
        ]);

        $response = $this->actingAs($this->user)->get('/reports');

        $response->assertOk()
            ->assertSee('Learning Reports')
            ->assertSee('HRM & Finance')
            ->assertSee('Recent exports');
    }

    public function test_report_cards_are_hidden_without_module_permissions()
    {
        $response = $this->actingAs($this->user)->get('/reports');

        $response->assertOk()
            ->assertDontSee('Learning Reports')
            ->assertDontSee('HRM & Finance')
            ->assertSee('Profile Reports');

        $this->user->givePermissionTo([
            Permission::firstOrCreate(['name' => 'view courses']),
            Permission::firstOrCreate(['name' => 'view finances']),
        ]);

        $this->actingAs($this->user)->get('/reports')
            ->assertOk()
            ->assertSee('Learning Reports')
            ->assertSee('Journal Entries');
    }

    public function test_course_and_certificate_exports_work_with_permissions()
    {
        $this->user->givePermissionTo([
            Permission::firstOrCreate(['name' => 'view courses']),
            Permission::firstOrCreate(['name' => 'view certificates']),
        ]);

        foreach (['courses', 'certificates', 'staff-incentives'] as $type) {
            if ($type === 'staff-incentives') {
                $this->user->givePermissionTo(Permission::firstOrCreate(['name' => 'view staff incentives']));
            }

            $this->actingAs($this->user)
                ->get("/reports/export/{$type}/csv")
                ->assertOk()
                ->assertHeader('Content-Type', 'text/csv; charset=utf-8');
        }
    }

    public function test_module_exports_require_their_module_permission()
    {
        $this->actingAs($this->user)
            ->get('/reports/export/journal-entries/csv')
            ->assertForbidden();

        $this->user->givePermissionTo(Permission::firstOrCreate(['name' => 'view finances']));

        $this->actingAs($this->user)
            ->get('/reports/export/journal-entries/csv')
            ->assertOk();
    }

    public function test_csv_export_works()
    {
        $response = $this->actingAs($this->user)->get('/reports/export/profiles/csv');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    }

    public function test_pdf_export_works()
    {
        $response = $this->actingAs($this->user)->get('/reports/export/profiles/pdf');
        $response->assertStatus(200);
    }

    public function test_excel_export_works()
    {
        $response = $this->actingAs($this->user)->get('/reports/export/profiles/excel');
        $response->assertStatus(200);
    }
}
