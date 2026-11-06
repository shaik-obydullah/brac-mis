<?php

namespace Tests\Feature;

use App\Models\ImmediateSupport;
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

    public function test_marking_a_follow_up_as_reintegrated_updates_the_user_profile()
    {
        $recipient = User::factory()->potentialCustomer()->create();
        $support = ImmediateSupport::create([
            'support_type' => 'food',
            'user_id' => $recipient->id,
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
        $this->assertSame(User::PROFILE_REINTEGRATED, $recipient->fresh()->profile_type);
    }

    public function test_reintegration_cannot_be_marked_for_a_support_without_a_user_profile()
    {
        $support = ImmediateSupport::create([
            'support_type' => 'food',
            'recipient_name' => 'Walk-in Recipient',
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

    public function test_a_follow_up_without_reintegration_does_not_change_the_profile_type()
    {
        $recipient = User::factory()->potentialCustomer()->create();
        $support = ImmediateSupport::create([
            'support_type' => 'financial',
            'user_id' => $recipient->id,
            'support_date' => '2026-11-01',
            'status' => 'provided',
        ]);

        $this->actingAs($this->user)->post('/support-follow-ups', [
            'immediate_support_id' => $support->id,
            'follow_up_date' => '2026-11-06',
            'method' => 'home_visit',
            'status' => 'completed',
        ])->assertSessionHasNoErrors();

        $this->assertSame(User::PROFILE_POTENTIAL_CUSTOMER, $recipient->fresh()->profile_type);
        $this->assertFalse(SupportFollowUp::firstOrFail()->is_reintegrated);
    }
}
