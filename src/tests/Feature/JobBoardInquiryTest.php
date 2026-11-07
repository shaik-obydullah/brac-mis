<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Country;
use App\Models\JobBoardInquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class JobBoardInquiryTest extends TestCase
{
    use RefreshDatabase;

    private function staffUser(): User
    {
        $permission = Permission::firstOrCreate(['name' => 'manage job board']);
        $role = Role::firstOrCreate(['name' => 'job-board-admin']);
        $role->givePermissionTo($permission);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    /**
     * @return array<string, string>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'employer_name' => 'Hiring Manager',
            'employer_organization' => 'Example Garments Ltd',
            'employer_email' => 'hr@example.test',
            'employer_phone' => '01711111111',
            'job_title' => 'Tailor',
            'message' => 'We would like to interview this candidate for a full-time tailoring role.',
            'consent' => '1',
        ], $overrides);
    }

    public function test_public_job_board_does_not_expose_private_contact_or_employer_details()
    {
        $country = Country::factory()->create(['name' => 'Saudi Arabia']);
        $branch = Branch::factory()->create(['district' => 'Dhaka', 'division' => 'Dhaka']);

        $candidate = User::factory()->potentialCustomer()->create([
            'name' => 'Privacy Candidate',
            'email' => 'private.candidate@example.test',
            'phone' => '01790000000',
            'branch_id' => $branch->id,
        ]);

        $candidate->skills()->create([
            'skill_name' => 'Tailoring',
            'proficiency' => 'advanced',
        ]);

        $candidate->migrationHistories()->create([
            'country_id' => $country->id,
            'employer_name' => 'Secret Employer Factory',
            'job_title' => 'Tailor',
            'visa_type' => 'SPECIALVISA',
            'start_date' => '2020-01-01',
            'end_date' => '2022-01-01',
            'status' => 'completed',
            'notes' => 'Private employer feedback',
        ]);

        $listing = $this->get('/job-board')->assertOk();
        $listing->assertSee('Privacy Candidate')
            ->assertSee('Contact via BRAC')
            ->assertDontSee('01790000000')
            ->assertDontSee('private.candidate@example.test')
            ->assertDontSee('Secret Employer Factory');

        $profile = $this->get('/job-board/'.$candidate->id)->assertOk();
        $profile->assertSee('Privacy Candidate')
            ->assertSee('Tailoring')
            ->assertSee('Saudi Arabia')
            ->assertSee('Request contact via BRAC')
            ->assertDontSee('01790000000')
            ->assertDontSee('private.candidate@example.test')
            ->assertDontSee('Secret Employer Factory')
            ->assertDontSee('SPECIALVISA')
            ->assertDontSee('Private employer feedback');
    }

    public function test_job_board_search_cannot_be_used_to_confirm_a_phone_number_or_email()
    {
        User::factory()->potentialCustomer()->create([
            'name' => 'Search Privacy Candidate',
            'email' => 'hidden@example.test',
            'phone' => '01795555555',
        ]);

        $this->get('/job-board?search=01795555555')
            ->assertOk()
            ->assertSee('No job seekers found')
            ->assertDontSee('Search Privacy Candidate');

        $this->get('/job-board?search=hidden@example.test')
            ->assertOk()
            ->assertSee('No job seekers found')
            ->assertDontSee('Search Privacy Candidate');
    }

    public function test_only_potential_customers_can_receive_contact_requests()
    {
        $candidate = User::factory()->create();

        $this->post('/job-board/'.$candidate->id.'/contact', $this->validPayload())
            ->assertNotFound();

        $this->assertDatabaseCount('job_board_inquiries', 0);
    }

    public function test_an_employer_can_submit_a_contact_request_through_brac()
    {
        $candidate = User::factory()->potentialCustomer()->create();

        $this->post('/job-board/'.$candidate->id.'/contact', $this->validPayload())
            ->assertRedirect(route('job-board.show', $candidate))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('job_board_inquiries', [
            'user_id' => $candidate->id,
            'employer_name' => 'Hiring Manager',
            'employer_organization' => 'Example Garments Ltd',
            'employer_email' => 'hr@example.test',
            'job_title' => 'Tailor',
            'status' => 'new',
        ]);

        $inquiry = JobBoardInquiry::firstOrFail();
        $this->assertNotNull($inquiry->ip_address);
        $this->assertSame($candidate->id, $inquiry->candidate->id);
    }

    public function test_contact_requests_require_valid_details_and_consent()
    {
        $candidate = User::factory()->potentialCustomer()->create();

        $this->post('/job-board/'.$candidate->id.'/contact', [
            'employer_name' => '',
            'employer_email' => 'not-an-email',
            'message' => 'Too short',
            'consent' => '',
        ])->assertSessionHasErrors(['employer_name', 'employer_email', 'message', 'consent']);

        $this->assertDatabaseCount('job_board_inquiries', 0);
    }

    public function test_honeypot_submissions_are_ignored()
    {
        $candidate = User::factory()->potentialCustomer()->create();

        $this->post('/job-board/'.$candidate->id.'/contact', $this->validPayload([
            'company_website' => 'https://spam.example',
        ]))->assertRedirect(route('job-board.show', $candidate));

        $this->assertDatabaseCount('job_board_inquiries', 0);
    }

    public function test_staff_with_permission_can_review_and_update_an_inquiry()
    {
        $staff = $this->staffUser();
        $candidate = User::factory()->potentialCustomer()->create([
            'name' => 'Inquiry Candidate',
            'email' => 'candidate@example.test',
            'phone' => '01712222222',
        ]);

        $inquiry = JobBoardInquiry::create([
            'user_id' => $candidate->id,
            'employer_name' => 'Hiring Manager',
            'employer_organization' => 'Example Garments Ltd',
            'employer_email' => 'hr@example.test',
            'job_title' => 'Tailor',
            'message' => 'We would like to interview this candidate for a full-time tailoring role.',
            'status' => 'new',
        ]);

        $this->actingAs($staff)
            ->get('/job-board/inquiries')
            ->assertOk()
            ->assertSee('Job Board Inquiries')
            ->assertSee('Hiring Manager')
            ->assertSee('Inquiry Candidate');

        $this->actingAs($staff)
            ->get('/job-board/inquiries/'.$inquiry->id)
            ->assertOk()
            ->assertSee('Inquiry Candidate')
            ->assertSee('01712222222')
            ->assertSee('candidate@example.test')
            ->assertSee('Example Garments Ltd');

        $this->actingAs($staff)
            ->patch('/job-board/inquiries/'.$inquiry->id, ['status' => 'contacted'])
            ->assertRedirect(route('job-board.inquiries.show', $inquiry))
            ->assertSessionHas('success');

        $inquiry->refresh();

        $this->assertSame('contacted', $inquiry->status);
        $this->assertSame($staff->id, $inquiry->handled_by);
        $this->assertNotNull($inquiry->handled_at);
    }

    public function test_staff_without_permission_cannot_access_the_inquiry_inbox()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/job-board/inquiries')
            ->assertForbidden();
    }

    public function test_inquiry_status_changes_are_recorded_in_the_audit_log()
    {
        $staff = $this->staffUser();
        $candidate = User::factory()->potentialCustomer()->create();

        $inquiry = JobBoardInquiry::create([
            'user_id' => $candidate->id,
            'employer_name' => 'Hiring Manager',
            'employer_email' => 'hr@example.test',
            'message' => 'We would like to interview this candidate for a full-time tailoring role.',
            'status' => 'new',
        ]);

        $this->actingAs($staff)
            ->patch('/job-board/inquiries/'.$inquiry->id, ['status' => 'closed'])
            ->assertRedirect(route('job-board.inquiries.show', $inquiry));

        $this->assertDatabaseHas('audit_logs', [
            'subject_type' => JobBoardInquiry::class,
            'subject_id' => $inquiry->id,
            'action' => 'updated',
        ]);

        $this->assertSame('Job Board Inquiry', AuditLog::subjectLabelFor(JobBoardInquiry::class));
    }

    public function test_dashboard_shows_new_job_board_inquiries_first_for_staff()
    {
        $staff = $this->staffUser();
        $candidate = User::factory()->potentialCustomer()->create(['name' => 'Dashboard Candidate']);

        JobBoardInquiry::create([
            'user_id' => $candidate->id,
            'employer_name' => 'Dashboard Employer',
            'employer_organization' => 'Example Garments Ltd',
            'employer_email' => 'hr@example.test',
            'job_title' => 'Tailor',
            'message' => 'We would like to interview this candidate for a full-time tailoring role.',
            'status' => 'new',
        ]);

        $this->actingAs($staff)
            ->get('/')
            ->assertOk()
            ->assertSee('Job board inquiries')
            ->assertSee('Open inbox')
            ->assertSee('Dashboard Candidate')
            ->assertSee('Dashboard Employer');
    }

    public function test_dashboard_hides_job_board_inquiries_without_permission()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/')
            ->assertOk()
            ->assertDontSee('Job board inquiries')
            ->assertDontSee('Open inbox');
    }
}
