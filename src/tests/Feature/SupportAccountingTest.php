<?php

namespace Tests\Feature;

use App\Models\ImmediateSupport;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SupportAccountingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'support-officer']);

        foreach ([
            'view immediate support', 'create immediate support',
            'edit immediate support', 'delete immediate support', 'view finances',
        ] as $permission) {
            Permission::firstOrCreate(['name' => $permission])->assignRole($role);
        }

        $this->user = User::factory()->create();
        $this->user->assignRole($role);
    }

    private function createSupport(array $overrides = [])
    {
        return $this->actingAs($this->user)->post('/support/immediate', array_merge([
            'support_type' => 'food',
            'recipient_name' => 'Support Recipient',
            'support_date' => '2026-11-06',
            'status' => 'provided',
            'amount' => 500,
        ], $overrides));
    }

    public function test_amount_creates_a_balanced_posted_journal_entry()
    {
        $this->createSupport()->assertSessionHasNoErrors();

        $support = ImmediateSupport::firstOrFail();
        $entry = JournalEntry::firstOrFail();

        $this->assertSame($entry->id, $support->journal_entry_id);
        $this->assertSame(JournalEntry::STATUS_POSTED, $entry->status);
        $this->assertSame('SUP-'.$support->id, $entry->reference);
        $this->assertNotNull($entry->posted_at);
        $this->assertCount(2, $entry->lines);
        $this->assertEquals(500, (float) $entry->total_debit);
        $this->assertEquals(500, (float) $entry->total_credit);
        $this->assertTrue($entry->is_balanced);

        $debitLine = $entry->lines->firstWhere(fn ($line) => (float) $line->debit > 0);
        $creditLine = $entry->lines->firstWhere(fn ($line) => (float) $line->credit > 0);

        $this->assertSame('5100', $debitLine->account->code);
        $this->assertSame('1000', $creditLine->account->code);

        $this->actingAs($this->user)
            ->get(route('support.show', ['category' => 'immediate', 'immediateSupport' => $support]))
            ->assertStatus(200)
            ->assertSee($entry->entry_no);
    }

    public function test_support_create_and_edit_forms_render_the_paid_from_selector()
    {
        $this->actingAs($this->user)
            ->get('/support/immediate/create')
            ->assertStatus(200)
            ->assertSee('Paid From');

        $this->createSupport()->assertSessionHasNoErrors();

        $support = ImmediateSupport::firstOrFail();

        $this->actingAs($this->user)
            ->get(route('support.edit', ['category' => 'immediate', 'immediateSupport' => $support]))
            ->assertStatus(200)
            ->assertSee('Paid From');
    }

    public function test_support_paid_from_bank_credits_the_bank_account()
    {
        $this->createSupport(['amount' => 500, 'paid_from' => 'bank'])->assertSessionHasNoErrors();

        $support = ImmediateSupport::firstOrFail();
        $creditLine = $support->journalEntry->lines->firstWhere(fn ($line) => (float) $line->credit > 0);

        $this->assertSame('bank', $support->paid_from);
        $this->assertSame('1010', $creditLine->account->code);
    }

    public function test_updating_paid_from_moves_the_credit_line()
    {
        $this->createSupport(['amount' => 500, 'paid_from' => 'cash'])->assertSessionHasNoErrors();

        $support = ImmediateSupport::firstOrFail();
        $entryId = $support->journal_entry_id;

        $this->actingAs($this->user)->put('/support/immediate/'.$support->id, [
            'support_type' => 'food',
            'recipient_name' => 'Support Recipient',
            'support_date' => '2026-11-06',
            'status' => 'provided',
            'amount' => 500,
            'paid_from' => 'bank',
        ])->assertSessionHasNoErrors();

        $entry = JournalEntry::firstOrFail();
        $creditLine = $entry->lines->firstWhere(fn ($line) => (float) $line->credit > 0);

        $this->assertSame($entryId, $entry->id);
        $this->assertSame('1010', $creditLine->account->code);
    }

    public function test_invalid_paid_from_value_is_rejected()
    {
        $this->createSupport(['amount' => 500, 'paid_from' => 'cheque'])->assertSessionHasErrors('paid_from');

        $this->assertSame(0, ImmediateSupport::count());
        $this->assertSame(0, JournalEntry::count());
    }

    public function test_posted_support_appears_in_finance_reports()
    {
        $this->createSupport(['amount' => 500])->assertSessionHasNoErrors();

        $response = $this->actingAs($this->user)
            ->get('/finances/income-statement?from=2000-01-01&to=2100-01-01');

        $response->assertStatus(200);
        $response->assertViewHas('totalExpense', fn ($value) => abs($value - 500) < 0.005);
    }

    public function test_each_support_category_posts_to_its_own_expense_account()
    {
        $categories = [
            ['category' => 'immediate', 'type' => 'food', 'expense' => '5100'],
            ['category' => 'mental-psychological', 'type' => 'mental_psychological', 'expense' => '5110'],
            ['category' => 'physical', 'type' => 'physical', 'expense' => '5120'],
            ['category' => 'financial', 'type' => 'financial', 'expense' => '5130'],
        ];

        foreach ($categories as $index => $row) {
            $this->actingAs($this->user)->post('/support/'.$row['category'], [
                'support_type' => $row['type'],
                'recipient_name' => 'Recipient '.$index,
                'support_date' => '2026-11-06',
                'status' => 'provided',
                'amount' => 100 + $index,
            ])->assertSessionHasNoErrors();

            $support = ImmediateSupport::where('support_type', $row['type'])->firstOrFail();
            $debitLine = $support->journalEntry->lines->firstWhere(fn ($line) => (float) $line->debit > 0);

            $this->assertSame($row['expense'], $debitLine->account->code);
        }

        $this->assertSame(4, JournalEntry::count());
    }

    public function test_support_without_an_amount_does_not_create_a_journal_entry()
    {
        $this->createSupport(['amount' => null])->assertSessionHasNoErrors();

        $support = ImmediateSupport::firstOrFail();

        $this->assertNull($support->journal_entry_id);
        $this->assertSame(0, JournalEntry::count());
    }

    public function test_pending_support_is_posted_as_a_draft_until_provided()
    {
        $this->createSupport(['status' => 'pending', 'amount' => 250])->assertSessionHasNoErrors();

        $support = ImmediateSupport::firstOrFail();
        $entry = $support->journalEntry;

        $this->assertSame(JournalEntry::STATUS_DRAFT, $entry->status);
        $this->assertNull($entry->posted_at);

        $this->actingAs($this->user)->put('/support/immediate/'.$support->id, [
            'support_type' => 'food',
            'recipient_name' => 'Support Recipient',
            'support_date' => '2026-11-06',
            'status' => 'provided',
            'amount' => 250,
        ])->assertSessionHasNoErrors();

        $entry->refresh();

        $this->assertSame(JournalEntry::STATUS_POSTED, $entry->status);
        $this->assertNotNull($entry->posted_at);
    }

    public function test_updating_the_amount_updates_the_same_journal_entry()
    {
        $this->createSupport(['amount' => 500])->assertSessionHasNoErrors();

        $support = ImmediateSupport::firstOrFail();
        $entryId = $support->journal_entry_id;

        $this->actingAs($this->user)->put('/support/immediate/'.$support->id, [
            'support_type' => 'food',
            'recipient_name' => 'Support Recipient',
            'support_date' => '2026-11-06',
            'status' => 'provided',
            'amount' => 750,
        ])->assertSessionHasNoErrors();

        $entry = JournalEntry::firstOrFail();

        $this->assertSame($entryId, $entry->id);
        $this->assertSame(1, JournalEntry::count());
        $this->assertEquals(750, (float) $entry->total_debit);
        $this->assertEquals(750, (float) $entry->total_credit);
    }

    public function test_cancelling_support_removes_the_journal_entry()
    {
        $this->createSupport(['amount' => 500])->assertSessionHasNoErrors();

        $support = ImmediateSupport::firstOrFail();

        $this->actingAs($this->user)->put('/support/immediate/'.$support->id, [
            'support_type' => 'food',
            'recipient_name' => 'Support Recipient',
            'support_date' => '2026-11-06',
            'status' => 'cancelled',
            'amount' => 500,
        ])->assertSessionHasNoErrors();

        $this->assertSame(0, JournalEntry::count());
        $this->assertNull($support->fresh()->journal_entry_id);
    }

    public function test_deleting_support_removes_the_journal_entry()
    {
        $this->createSupport(['amount' => 500])->assertSessionHasNoErrors();

        $support = ImmediateSupport::firstOrFail();

        $this->actingAs($this->user)
            ->delete('/support/immediate/'.$support->id)
            ->assertRedirect(route('support.index', ['category' => 'immediate']));

        $this->assertDatabaseMissing('immediate_supports', ['id' => $support->id]);
        $this->assertSame(0, JournalEntry::count());
        $this->assertSame(0, JournalLine::count());
    }

    public function test_support_accounting_rolls_back_when_the_support_cannot_be_saved()
    {
        $this->actingAs($this->user)->post('/support/immediate', [
            'support_type' => 'food',
            'support_date' => '2026-11-06',
            'status' => 'provided',
            'amount' => 500,
        ])->assertSessionHasErrors('recipient_name');

        $this->assertSame(0, ImmediateSupport::count());
        $this->assertSame(0, JournalEntry::count());
    }
}
