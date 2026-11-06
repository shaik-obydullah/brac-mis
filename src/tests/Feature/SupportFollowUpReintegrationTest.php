<?php

namespace Tests\Feature;

use App\Models\Beneficiary;
use App\Models\ImmediateSupport;
use App\Models\Returnee;
use App\Models\SupportFollowUp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SupportFollowUpReintegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'support-officer']);

        foreach ([
            'view immediate support', 'create immediate support',
            'edit immediate support', 'delete immediate support',
        ] as $permission) {
            Permission::firstOrCreate(['name' => $permission])->assignRole($role);
        }

        $this->user = User::factory()->create();
        $this->user->assignRole($role);
    }

    public function test_marking_a_follow_up_as_reintegrated_completes_the_returnee()
    {
        $returnee = Returnee::factory()->create(['current_status' => 'in_progress']);
        $support = ImmediateSupport::create([
            'support_type' => 'food',
            'returnee_id' => $returnee->id,
            'recipient_name' => 'Returnee Recipient',
            'support_date' => '2026-11-01',
            'status' => 'provided',
        ]);

        $response = $this->actingAs($this->user)->post('/support-follow-ups', [
            'immediate_support_id' => $support->id,
            'follow_up_date' => '2026-11-06',
            'method' => 'phone_call',
            'status' => 'completed',
            'outcome' => 'Back in stable employment.',
            'is_reintegrated' => '1',
        ]);

        $followUp = SupportFollowUp::firstOrFail();

        $response->assertRedirect(route('support.show', [
            'category' => 'immediate',
            'immediateSupport' => $support->id,
        ]));
        $this->assertTrue($followUp->is_reintegrated);
        $this->assertNotNull($followUp->reintegrated_at);
        $this->assertSame('completed', $returnee->fresh()->current_status);
        $this->assertSame('Reintegrated', $returnee->fresh()->status_label);
    }

    public function test_reintegration_cannot_be_marked_for_a_support_without_a_returnee()
    {
        $beneficiary = Beneficiary::factory()->create();
        $support = ImmediateSupport::create([
            'support_type' => 'food',
            'beneficiary_id' => $beneficiary->id,
            'support_date' => '2026-11-01',
            'status' => 'provided',
        ]);

        $response = $this->actingAs($this->user)->post('/support-follow-ups', [
            'immediate_support_id' => $support->id,
            'follow_up_date' => '2026-11-06',
            'method' => 'phone_call',
            'status' => 'completed',
            'is_reintegrated' => '1',
        ]);

        $response->assertSessionHasErrors('is_reintegrated');
        $this->assertDatabaseCount('support_follow_ups', 0);
    }

    public function test_a_follow_up_without_reintegration_does_not_change_the_returnee_status()
    {
        $returnee = Returnee::factory()->create(['current_status' => 'planning']);
        $support = ImmediateSupport::create([
            'support_type' => 'financial',
            'returnee_id' => $returnee->id,
            'support_date' => '2026-11-01',
            'status' => 'provided',
        ]);

        $this->actingAs($this->user)->post('/support-follow-ups', [
            'immediate_support_id' => $support->id,
            'follow_up_date' => '2026-11-06',
            'method' => 'home_visit',
            'status' => 'completed',
        ])->assertSessionHasNoErrors();

        $this->assertSame('planning', $returnee->fresh()->current_status);
        $this->assertFalse(SupportFollowUp::firstOrFail()->is_reintegrated);
    }
}
