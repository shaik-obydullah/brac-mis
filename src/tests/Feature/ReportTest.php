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

    public function test_csv_export_works()
    {
        $response = $this->actingAs($this->user)->get('/reports/export/beneficiaries/csv');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    }

    public function test_pdf_export_works()
    {
        $response = $this->actingAs($this->user)->get('/reports/export/beneficiaries/pdf');
        $response->assertStatus(200);
    }

    public function test_excel_export_works()
    {
        $response = $this->actingAs($this->user)->get('/reports/export/beneficiaries/excel');
        $response->assertStatus(200);
    }
}
