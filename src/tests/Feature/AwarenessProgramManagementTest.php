<?php

namespace Tests\Feature;

use App\Models\AwarenessProgram;
use App\Models\AwarenessProgramOutcome;
use App\Models\User;
use Database\Seeders\AwarenessProgramSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AwarenessProgramManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'branch-admin']);
        foreach ([
            'view awareness programs', 'create awareness programs',
            'edit awareness programs', 'delete awareness programs',
        ] as $permission) {
            Permission::create(['name' => $permission])->assignRole($role);
        }

        $this->user = User::factory()->create();
        $this->user->assignRole($role);
    }

    public function test_awareness_programs_list_is_accessible()
    {
        AwarenessProgram::create([
            'program_name' => 'Safe Migration Session',
            'division' => 'Dhaka',
            'district' => 'Dhaka',
            'upazila' => 'Savar',
        ]);

        $this->actingAs($this->user)->get('/awareness-programs')->assertStatus(200);
    }

    public function test_awareness_program_can_be_created()
    {
        $response = $this->actingAs($this->user)->post('/awareness-programs', [
            'program_name' => 'Community Awareness',
            'division' => 'Sylhet',
            'district' => 'Sylhet',
            'upazila' => 'Sylhet Sadar',
            'address' => 'Zindabazar, Sylhet',
        ]);

        $response->assertRedirect('/awareness-programs');
        $this->assertDatabaseHas('awareness_programs', [
            'program_name' => 'Community Awareness',
            'division' => 'Sylhet',
            'district' => 'Sylhet',
            'upazila' => 'Sylhet Sadar',
        ]);
    }

    public function test_awareness_program_can_be_updated()
    {
        $program = AwarenessProgram::create([
            'program_name' => 'Old Program',
            'division' => 'Dhaka',
            'district' => 'Dhaka',
            'upazila' => 'Savar',
        ]);

        $response = $this->actingAs($this->user)->put("/awareness-programs/{$program->id}", [
            'program_name' => 'Updated Program',
            'division' => 'Khulna',
            'district' => 'Khulna',
            'upazila' => 'Khalishpur',
            'address' => 'Khalishpur, Khulna',
        ]);

        $response->assertRedirect('/awareness-programs');
        $this->assertDatabaseHas('awareness_programs', [
            'id' => $program->id,
            'program_name' => 'Updated Program',
            'division' => 'Khulna',
            'district' => 'Khulna',
            'upazila' => 'Khalishpur',
        ]);
    }

    public function test_participant_outcomes_can_be_added_to_a_program()
    {
        $program = $this->program(['program_name' => 'Outcome Session']);

        $this->actingAs($this->user)
            ->post("/awareness-programs/{$program->id}/outcomes", [
                'participant_name' => 'Rahim Uddin',
                'participant_phone' => '01700000000',
                'understood' => '1',
                'notes' => 'Answered all follow-up questions.',
            ])
            ->assertRedirect(route('awareness-programs.show', $program));

        $this->assertDatabaseHas('awareness_program_outcomes', [
            'awareness_program_id' => $program->id,
            'participant_name' => 'Rahim Uddin',
            'participant_phone' => '01700000000',
            'understood' => true,
        ]);
    }

    public function test_participant_outcome_can_be_updated()
    {
        $program = $this->program();
        $outcome = $program->outcomes()->create([
            'participant_name' => 'Karim Mia',
            'understood' => true,
        ]);

        $this->actingAs($this->user)
            ->patch("/awareness-programs/{$program->id}/outcomes/{$outcome->id}", [
                'participant_name' => 'Karim Mia',
                'understood' => '0',
            ])
            ->assertRedirect(route('awareness-programs.show', $program));

        $this->assertFalse($outcome->fresh()->understood);
    }

    public function test_participant_outcome_can_be_deleted()
    {
        $program = $this->program();
        $outcome = $program->outcomes()->create([
            'participant_name' => 'Jamil Ahmed',
            'understood' => true,
        ]);

        $this->actingAs($this->user)
            ->delete("/awareness-programs/{$program->id}/outcomes/{$outcome->id}")
            ->assertRedirect(route('awareness-programs.show', $program));

        $this->assertDatabaseMissing('awareness_program_outcomes', ['id' => $outcome->id]);
    }

    public function test_list_shows_the_percentage_of_participants_who_understood_the_message()
    {
        $program = $this->program();
        $program->outcomes()->create(['participant_name' => 'Participant One', 'understood' => true]);
        $program->outcomes()->create(['participant_name' => 'Participant Two', 'understood' => true]);
        $program->outcomes()->create(['participant_name' => 'Participant Three', 'understood' => false]);

        $this->actingAs($this->user)
            ->get('/awareness-programs')
            ->assertStatus(200)
            ->assertSee('67%')
            ->assertSee('2 of 3 participants understood');
    }

    public function test_list_can_be_filtered_by_participant_understanding()
    {
        $understood = $this->program(['program_name' => 'Urban Session']);
        $understood->outcomes()->create(['participant_name' => 'Understood Person', 'understood' => true]);

        $notUnderstood = $this->program(['program_name' => 'Rural Session']);
        $notUnderstood->outcomes()->create(['participant_name' => 'Confused Person', 'understood' => false]);

        $this->program(['program_name' => 'Untested Session']);

        $this->actingAs($this->user)
            ->get('/awareness-programs?understanding=understood')
            ->assertStatus(200)
            ->assertSee('Urban Session')
            ->assertDontSee('Rural Session')
            ->assertDontSee('Untested Session');

        $this->actingAs($this->user)
            ->get('/awareness-programs?understanding=not_understood')
            ->assertStatus(200)
            ->assertSee('Rural Session')
            ->assertDontSee('Urban Session');

        $this->actingAs($this->user)
            ->get('/awareness-programs?understanding=not_tested')
            ->assertStatus(200)
            ->assertSee('Untested Session')
            ->assertDontSee('Urban Session');
    }

    public function test_awareness_outcomes_page_lists_participants_across_programs()
    {
        $program = $this->program(['program_name' => 'Outcome Listing Program']);
        $program->outcomes()->create(['participant_name' => 'Listed Participant', 'understood' => true]);

        $other = $this->program(['program_name' => 'Second Program']);
        $other->outcomes()->create(['participant_name' => 'Second Participant', 'understood' => false]);

        $this->actingAs($this->user)
            ->get('/awareness-outcomes')
            ->assertStatus(200)
            ->assertSee('Listed Participant')
            ->assertSee('Second Participant')
            ->assertSee('Outcome Listing Program')
            ->assertSee('Second Program');
    }

    public function test_awareness_program_can_be_deleted()
    {
        $program = AwarenessProgram::create([
            'program_name' => 'Temporary Program',
            'division' => 'Dhaka',
            'district' => 'Dhaka',
            'upazila' => 'Savar',
        ]);

        $response = $this->actingAs($this->user)->delete("/awareness-programs/{$program->id}");

        $response->assertRedirect('/awareness-programs');
        $this->assertDatabaseMissing('awareness_programs', ['id' => $program->id]);
    }

    public function test_awareness_program_seeder_creates_sample_programs_and_outcomes()
    {
        $this->seed(AwarenessProgramSeeder::class);

        $this->assertSame(10, AwarenessProgram::count());
        $this->assertSame(40, AwarenessProgramOutcome::count());

        $program = AwarenessProgram::where('program_name', 'Anti-Trafficking Awareness Session')->firstOrFail();

        $this->assertSame('Dhaka', $program->division);
        $this->assertSame('Dhaka', $program->district);
        $this->assertSame('Savar', $program->upazila);
        $this->assertSame('Savar Union Parishad Hall', $program->address);
        $this->assertSame(6, $program->outcomes()->count());
        $this->assertSame(5, $program->outcomes()->where('understood', true)->count());

        $this->assertSame(
            2,
            AwarenessProgram::whereDoesntHave('outcomes')->count()
        );
    }

    public function test_awareness_program_seeder_is_idempotent()
    {
        $this->seed(AwarenessProgramSeeder::class);
        $this->seed(AwarenessProgramSeeder::class);

        $this->assertSame(10, AwarenessProgram::count());
        $this->assertSame(40, AwarenessProgramOutcome::count());
    }

    private function program(array $attributes = []): AwarenessProgram
    {
        return AwarenessProgram::create(array_merge([
            'program_name' => 'Awareness Program',
            'division' => 'Dhaka',
            'district' => 'Dhaka',
            'upazila' => 'Savar',
        ], $attributes));
    }
}
