<?php

namespace Tests\Feature;

use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\Staff;
use App\Models\StaffIncentive;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StaffIncentiveAccountingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Staff $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'hrm-accountant']);

        foreach ([
            'view staff', 'view staff incentives', 'create staff incentives',
            'edit staff incentives', 'delete staff incentives', 'view finances',
        ] as $permission) {
            Permission::firstOrCreate(['name' => $permission])->assignRole($role);
        }

        $this->user = User::factory()->create();
        $this->user->assignRole($role);
        $this->staff = Staff::factory()->create();
    }

    private function createIncentive(array $overrides = [])
    {
        return $this->actingAs($this->user)->post('/staff-incentives', array_merge([
            'staff_id' => $this->staff->id,
            'title' => 'Performance bonus',
            'amount' => 500,
            'incentive_date' => '2026-11-06',
            'status' => StaffIncentive::STATUS_PAID,
        ], $overrides));
    }

    public function test_paid_incentive_creates_a_balanced_posted_journal_entry()
    {
        $this->createIncentive()->assertSessionHasNoErrors();

        $incentive = StaffIncentive::firstOrFail();
        $entry = JournalEntry::firstOrFail();

        $this->assertSame($entry->id, $incentive->journal_entry_id);
        $this->assertSame(JournalEntry::STATUS_POSTED, $entry->status);
        $this->assertSame('INC-'.$incentive->id, $entry->reference);
        $this->assertNotNull($entry->posted_at);
        $this->assertCount(2, $entry->lines);
        $this->assertEquals(500, (float) $entry->total_debit);
        $this->assertEquals(500, (float) $entry->total_credit);
        $this->assertTrue($entry->is_balanced);

        $debitLine = $entry->lines->firstWhere(fn ($line) => (float) $line->debit > 0);
        $creditLine = $entry->lines->firstWhere(fn ($line) => (float) $line->credit > 0);

        $this->assertSame('5210', $debitLine->account->code);
        $this->assertSame('1000', $creditLine->account->code);

        $this->actingAs($this->user)
            ->get(route('staff-incentives.show', $incentive))
            ->assertStatus(200)
            ->assertSee($entry->entry_no);
    }

    public function test_incentive_create_and_edit_forms_render_the_paid_from_selector()
    {
        $this->actingAs($this->user)
            ->get('/staff-incentives/create')
            ->assertStatus(200)
            ->assertSee('Paid from');

        $this->createIncentive()->assertSessionHasNoErrors();

        $incentive = StaffIncentive::firstOrFail();

        $this->actingAs($this->user)
            ->get(route('staff-incentives.edit', $incentive))
            ->assertStatus(200)
            ->assertSee('Paid from');
    }

    public function test_incentive_paid_from_bank_credits_the_bank_account()
    {
        $this->createIncentive(['amount' => 500, 'paid_from' => 'bank'])->assertSessionHasNoErrors();

        $incentive = StaffIncentive::firstOrFail();
        $creditLine = $incentive->journalEntry->lines->firstWhere(fn ($line) => (float) $line->credit > 0);

        $this->assertSame('bank', $incentive->paid_from);
        $this->assertSame('1010', $creditLine->account->code);
    }

    public function test_updating_paid_from_moves_the_credit_line()
    {
        $this->createIncentive(['amount' => 500, 'paid_from' => 'cash'])->assertSessionHasNoErrors();

        $incentive = StaffIncentive::firstOrFail();
        $entryId = $incentive->journal_entry_id;

        $this->actingAs($this->user)->put('/staff-incentives/'.$incentive->id, [
            'staff_id' => $this->staff->id,
            'title' => 'Performance bonus',
            'amount' => 500,
            'incentive_date' => '2026-11-06',
            'status' => StaffIncentive::STATUS_PAID,
            'paid_from' => 'bank',
        ])->assertSessionHasNoErrors();

        $entry = JournalEntry::firstOrFail();
        $creditLine = $entry->lines->firstWhere(fn ($line) => (float) $line->credit > 0);

        $this->assertSame($entryId, $entry->id);
        $this->assertSame('1010', $creditLine->account->code);
    }

    public function test_invalid_paid_from_value_is_rejected()
    {
        $this->createIncentive(['amount' => 500, 'paid_from' => 'cheque'])->assertSessionHasErrors('paid_from');

        $this->assertSame(0, StaffIncentive::count());
        $this->assertSame(0, JournalEntry::count());
    }

    public function test_posted_incentive_appears_in_finance_reports()
    {
        $this->createIncentive(['amount' => 500])->assertSessionHasNoErrors();

        $response = $this->actingAs($this->user)
            ->get('/finances/income-statement?from=2000-01-01&to=2100-01-01');

        $response->assertStatus(200);
        $response->assertViewHas('totalExpense', fn ($value) => abs($value - 500) < 0.005);
    }

    public function test_pending_incentive_is_posted_as_a_draft_until_paid()
    {
        $this->createIncentive(['status' => StaffIncentive::STATUS_PENDING, 'amount' => 250])->assertSessionHasNoErrors();

        $incentive = StaffIncentive::firstOrFail();
        $entry = $incentive->journalEntry;

        $this->assertSame(JournalEntry::STATUS_DRAFT, $entry->status);
        $this->assertNull($entry->posted_at);

        $this->actingAs($this->user)->put('/staff-incentives/'.$incentive->id, [
            'staff_id' => $this->staff->id,
            'title' => 'Performance bonus',
            'amount' => 250,
            'incentive_date' => '2026-11-06',
            'status' => StaffIncentive::STATUS_PAID,
        ])->assertSessionHasNoErrors();

        $entry->refresh();

        $this->assertSame(JournalEntry::STATUS_POSTED, $entry->status);
        $this->assertNotNull($entry->posted_at);
    }

    public function test_updating_the_amount_updates_the_same_journal_entry()
    {
        $this->createIncentive(['amount' => 500])->assertSessionHasNoErrors();

        $incentive = StaffIncentive::firstOrFail();
        $entryId = $incentive->journal_entry_id;

        $this->actingAs($this->user)->put('/staff-incentives/'.$incentive->id, [
            'staff_id' => $this->staff->id,
            'title' => 'Performance bonus',
            'amount' => 750,
            'incentive_date' => '2026-11-06',
            'status' => StaffIncentive::STATUS_PAID,
        ])->assertSessionHasNoErrors();

        $entry = JournalEntry::firstOrFail();

        $this->assertSame($entryId, $entry->id);
        $this->assertSame(1, JournalEntry::count());
        $this->assertEquals(750, (float) $entry->total_debit);
        $this->assertEquals(750, (float) $entry->total_credit);
    }

    public function test_incentive_without_an_amount_does_not_create_a_journal_entry()
    {
        $this->createIncentive(['amount' => 0])->assertSessionHasNoErrors();

        $incentive = StaffIncentive::firstOrFail();

        $this->assertNull($incentive->journal_entry_id);
        $this->assertSame(0, JournalEntry::count());
    }

    public function test_deleting_incentive_removes_the_journal_entry()
    {
        $this->createIncentive(['amount' => 500])->assertSessionHasNoErrors();

        $incentive = StaffIncentive::firstOrFail();

        $this->actingAs($this->user)
            ->delete('/staff-incentives/'.$incentive->id)
            ->assertRedirect('/staff-incentives');

        $this->assertDatabaseMissing('staff_incentives', ['id' => $incentive->id]);
        $this->assertSame(0, JournalEntry::count());
        $this->assertSame(0, JournalLine::count());
    }

    public function test_staff_incentive_accounting_rolls_back_when_the_incentive_cannot_be_saved()
    {
        $this->actingAs($this->user)->post('/staff-incentives', [
            'staff_id' => 999999,
            'title' => 'Performance bonus',
            'amount' => 500,
            'incentive_date' => '2026-11-06',
            'status' => StaffIncentive::STATUS_PAID,
        ])->assertSessionHasErrors('staff_id');

        $this->assertSame(0, StaffIncentive::count());
        $this->assertSame(0, JournalEntry::count());
    }
}
