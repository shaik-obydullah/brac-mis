<?php

namespace Tests\Feature;

use App\Models\ImmediateSupport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ImmediateSupportManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::create(['name' => 'view immediate support']);
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

    public function test_each_support_category_has_a_standalone_page()
    {
        $this->actingAs($this->user)->get('/support/immediate')->assertStatus(200);
        $this->actingAs($this->user)->get('/support/mental-psychological')->assertStatus(200);
        $this->actingAs($this->user)->get('/support/physical')->assertStatus(200);
        $this->actingAs($this->user)->get('/support/financial')->assertStatus(200);
    }

    public function test_immediate_support_page_shows_its_three_types()
    {
        $this->actingAs($this->user)
            ->get('/support/immediate')
            ->assertStatus(200)
            ->assertSee('Immediate Support')
            ->assertSee('Food')
            ->assertSee('Bus Ticket')
            ->assertSee('Shelter');
    }

    public function test_category_pages_only_list_their_own_support_type()
    {
        foreach ([
            'food' => 'Food Recipient',
            'mental_psychological' => 'Mental Recipient',
            'physical' => 'Physical Recipient',
            'financial' => 'Financial Recipient',
        ] as $type => $recipient) {
            ImmediateSupport::create([
                'support_type' => $type,
                'recipient_name' => $recipient,
                'support_date' => '2026-11-01',
                'status' => 'provided',
            ]);
        }

        $this->actingAs($this->user)->get('/support/immediate')
            ->assertSee('Food Recipient')
            ->assertDontSee('Mental Recipient')
            ->assertDontSee('Physical Recipient')
            ->assertDontSee('Financial Recipient');

        $this->actingAs($this->user)->get('/support/mental-psychological')
            ->assertSee('Mental Recipient')
            ->assertDontSee('Food Recipient')
            ->assertDontSee('Physical Recipient')
            ->assertDontSee('Financial Recipient');

        $this->actingAs($this->user)->get('/support/physical')
            ->assertSee('Physical Recipient')
            ->assertDontSee('Food Recipient')
            ->assertDontSee('Mental Recipient')
            ->assertDontSee('Financial Recipient');

        $this->actingAs($this->user)->get('/support/financial')
            ->assertSee('Financial Recipient')
            ->assertDontSee('Food Recipient')
            ->assertDontSee('Mental Recipient')
            ->assertDontSee('Physical Recipient');
    }

    public function test_support_can_be_recorded_for_a_user_profile()
    {
        $recipient = User::factory()->create();

        $response = $this->actingAs($this->user)->post('/support/immediate', [
            'support_type' => 'food',
            'user_id' => $recipient->id,
            'support_date' => '2026-11-06',
            'status' => 'provided',
            'description' => 'Food package for one week.',
        ]);

        $support = ImmediateSupport::firstOrFail();

        $response->assertRedirect(route('support.show', ['category' => 'immediate', 'immediateSupport' => $support]));
        $this->assertSame('food', $support->support_type);
        $this->assertSame($recipient->id, $support->user_id);
        $this->assertSame($this->user->id, $support->provided_by);
    }

    public function test_financial_support_can_be_recorded_with_an_amount()
    {
        $recipient = User::factory()->create();

        $this->actingAs($this->user)->post('/support/financial', [
            'support_type' => 'financial',
            'user_id' => $recipient->id,
            'support_date' => '2026-11-06',
            'status' => 'provided',
            'amount' => 2500.50,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('immediate_supports', [
            'support_type' => 'financial',
            'amount' => 2500.50,
        ]);
    }

    public function test_a_category_rejects_another_categories_support_type()
    {
        $this->actingAs($this->user)->post('/support/physical', [
            'support_type' => 'food',
            'recipient_name' => 'Wrong Category',
            'support_date' => '2026-11-06',
            'status' => 'provided',
        ])->assertSessionHasErrors('support_type');

        $this->assertDatabaseMissing('immediate_supports', ['recipient_name' => 'Wrong Category']);
    }

    public function test_support_can_be_recorded_for_an_unregistered_recipient()
    {
        $this->actingAs($this->user)->post('/support/immediate', [
            'support_type' => 'bus_ticket',
            'recipient_name' => 'Walk-in Recipient',
            'recipient_phone' => '01700000000',
            'support_date' => '2026-11-06',
            'status' => 'provided',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('immediate_supports', [
            'support_type' => 'bus_ticket',
            'recipient_name' => 'Walk-in Recipient',
        ]);
    }

    public function test_recipient_is_required()
    {
        $this->actingAs($this->user)->post('/support/immediate', [
            'support_type' => 'shelter',
            'support_date' => '2026-11-06',
            'status' => 'provided',
        ])->assertSessionHasErrors('recipient_name');
    }

    public function test_support_can_be_updated()
    {
        $support = ImmediateSupport::create([
            'support_type' => 'mental_psychological',
            'recipient_name' => 'Support Recipient',
            'support_date' => '2026-11-01',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->put("/support/mental-psychological/{$support->id}", [
            'support_type' => 'mental_psychological',
            'recipient_name' => 'Support Recipient',
            'support_date' => '2026-11-02',
            'status' => 'provided',
            'notes' => 'Counselling completed.',
        ]);

        $response->assertRedirect(route('support.show', ['category' => 'mental-psychological', 'immediateSupport' => $support]));
        $this->assertDatabaseHas('immediate_supports', [
            'id' => $support->id,
            'status' => 'provided',
            'notes' => 'Counselling completed.',
        ]);
    }

    public function test_support_can_be_deleted()
    {
        $support = ImmediateSupport::create([
            'support_type' => 'physical',
            'recipient_name' => 'Support Recipient',
            'support_date' => '2026-11-01',
            'status' => 'provided',
        ]);

        $this->actingAs($this->user)
            ->delete("/support/physical/{$support->id}")
            ->assertRedirect(route('support.index', ['category' => 'physical']));

        $this->assertDatabaseMissing('immediate_supports', ['id' => $support->id]);
    }

    public function test_a_support_record_cannot_be_viewed_from_another_category()
    {
        $support = ImmediateSupport::create([
            'support_type' => 'food',
            'recipient_name' => 'Food Recipient',
            'support_date' => '2026-11-01',
            'status' => 'provided',
        ]);

        $this->actingAs($this->user)
            ->get("/support/physical/{$support->id}")
            ->assertNotFound();
    }

    public function test_old_immediate_support_url_redirects_to_the_standalone_page()
    {
        $this->actingAs($this->user)
            ->get('/immediate-supports')
            ->assertRedirect('/support/immediate');
    }

    public function test_immediate_support_requires_permission()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/support/immediate')->assertStatus(403);
    }
}
