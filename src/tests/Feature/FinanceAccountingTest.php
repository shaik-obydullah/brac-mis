<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\AuditLog;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FinanceAccountingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'finance-admin']);
        foreach (['view finances', 'create finances', 'edit finances', 'delete finances'] as $permission) {
            Permission::create(['name' => $permission])->assignRole($role);
        }

        $this->user = User::factory()->create();
        $this->user->assignRole($role);
    }

    private function postEntry(float $amount = 1500, string $status = JournalEntry::STATUS_POSTED)
    {
        $cash = Account::where('code', '1000')->firstOrFail();
        $income = Account::where('code', '4000')->firstOrFail();

        return $this->actingAs($this->user)->post('/finances', [
            'entry_date' => now()->toDateString(),
            'reference' => 'RCPT-001',
            'description' => 'Donation received',
            'status' => $status,
            'lines' => [
                ['account_id' => $cash->id, 'description' => 'Cash received', 'debit' => $amount, 'credit' => 0],
                ['account_id' => $income->id, 'description' => 'Donation income', 'debit' => 0, 'credit' => $amount],
            ],
        ]);
    }

    public function test_journal_index_is_accessible()
    {
        $this->actingAs($this->user)->get('/finances')->assertStatus(200);
    }

    public function test_journal_create_page_is_accessible()
    {
        $this->actingAs($this->user)->get('/finances/create')->assertStatus(200);
    }

    public function test_balanced_journal_entry_can_be_created()
    {
        $response = $this->postEntry();

        $entry = JournalEntry::firstOrFail();

        $response->assertRedirect(route('finances.show', $entry));
        $this->assertDatabaseHas('journal_entries', [
            'id' => $entry->id,
            'reference' => 'RCPT-001',
            'status' => JournalEntry::STATUS_POSTED,
        ]);
        $this->assertCount(2, $entry->lines);
        $this->assertTrue($entry->fresh('lines')->is_balanced);
    }

    public function test_unbalanced_journal_entry_is_rejected()
    {
        $cash = Account::where('code', '1000')->firstOrFail();
        $income = Account::where('code', '4000')->firstOrFail();

        $response = $this->actingAs($this->user)->post('/finances', [
            'entry_date' => now()->toDateString(),
            'status' => JournalEntry::STATUS_POSTED,
            'lines' => [
                ['account_id' => $cash->id, 'debit' => 500, 'credit' => 0],
                ['account_id' => $income->id, 'debit' => 0, 'credit' => 400],
            ],
        ]);

        $response->assertSessionHasErrors('lines');
        $this->assertDatabaseCount('journal_entries', 0);
    }

    public function test_entry_with_single_side_line_is_rejected()
    {
        $cash = Account::where('code', '1000')->firstOrFail();

        $response = $this->actingAs($this->user)->post('/finances', [
            'entry_date' => now()->toDateString(),
            'status' => JournalEntry::STATUS_POSTED,
            'lines' => [
                ['account_id' => $cash->id, 'debit' => 100, 'credit' => 0],
                ['account_id' => $cash->id, 'debit' => 100, 'credit' => 0],
            ],
        ]);

        $response->assertSessionHasErrors('lines');
    }

    public function test_journal_entry_show_and_edit_pages_render()
    {
        $this->postEntry(1500, JournalEntry::STATUS_DRAFT);

        $entry = JournalEntry::firstOrFail();

        $this->actingAs($this->user)->get(route('finances.show', $entry))->assertStatus(200);
        $this->actingAs($this->user)->get(route('finances.edit', $entry))->assertStatus(200);
    }

    public function test_journal_entry_can_be_updated()
    {
        $this->postEntry(1500, JournalEntry::STATUS_DRAFT);

        $entry = JournalEntry::firstOrFail();
        $cash = Account::where('code', '1000')->firstOrFail();
        $income = Account::where('code', '4000')->firstOrFail();

        $response = $this->actingAs($this->user)->put(route('finances.update', $entry), [
            'entry_date' => now()->toDateString(),
            'reference' => 'RCPT-002',
            'description' => 'Updated donation',
            'status' => JournalEntry::STATUS_POSTED,
            'lines' => [
                ['account_id' => $cash->id, 'debit' => 2500, 'credit' => 0],
                ['account_id' => $income->id, 'debit' => 0, 'credit' => 2500],
            ],
        ]);

        $response->assertRedirect(route('finances.show', $entry));
        $this->assertDatabaseHas('journal_entries', ['id' => $entry->id, 'reference' => 'RCPT-002']);
        $this->assertEquals(2500, (float) $entry->fresh('lines')->total_debit);
    }

    public function test_journal_entry_can_be_deleted()
    {
        $this->postEntry(1500, JournalEntry::STATUS_DRAFT);

        $entry = JournalEntry::firstOrFail();

        $response = $this->actingAs($this->user)->delete(route('finances.destroy', $entry));

        $response->assertRedirect(route('finances.index'));
        $this->assertDatabaseMissing('journal_entries', ['id' => $entry->id]);
        $this->assertDatabaseCount('journal_lines', 0);
    }

    public function test_posted_journal_entries_are_locked_for_regular_finance_users()
    {
        $this->postEntry(1500, JournalEntry::STATUS_POSTED);

        $entry = JournalEntry::firstOrFail();
        $cash = Account::where('code', '1000')->firstOrFail();
        $income = Account::where('code', '4000')->firstOrFail();

        $this->actingAs($this->user)->put(route('finances.update', $entry), [
            'entry_date' => now()->toDateString(),
            'reference' => 'RCPT-CHANGED',
            'description' => 'Rewritten history',
            'status' => JournalEntry::STATUS_POSTED,
            'lines' => [
                ['account_id' => $cash->id, 'debit' => 2500, 'credit' => 0],
                ['account_id' => $income->id, 'debit' => 0, 'credit' => 2500],
            ],
        ])->assertSessionHas('error');

        $this->assertSame('RCPT-001', $entry->fresh()->reference);

        $this->actingAs($this->user)
            ->delete(route('finances.destroy', $entry))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('journal_entries', ['id' => $entry->id]);
    }

    public function test_super_admin_can_modify_a_posted_journal_entry()
    {
        $this->postEntry(1500, JournalEntry::STATUS_POSTED);

        $entry = JournalEntry::firstOrFail();
        $cash = Account::where('code', '1000')->firstOrFail();
        $income = Account::where('code', '4000')->firstOrFail();

        $superAdminRole = Role::create(['name' => 'super-admin']);
        $superAdminRole->syncPermissions(Permission::all());
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole($superAdminRole);

        $this->actingAs($superAdmin)->put(route('finances.update', $entry), [
            'entry_date' => now()->toDateString(),
            'reference' => 'RCPT-SUPER',
            'description' => 'Corrected by super admin',
            'status' => JournalEntry::STATUS_POSTED,
            'lines' => [
                ['account_id' => $cash->id, 'debit' => 2500, 'credit' => 0],
                ['account_id' => $income->id, 'debit' => 0, 'credit' => 2500],
            ],
        ])->assertRedirect(route('finances.show', $entry));

        $this->assertDatabaseHas('journal_entries', ['id' => $entry->id, 'reference' => 'RCPT-SUPER']);
    }

    public function test_chart_of_accounts_pages_are_accessible()
    {
        $account = Account::where('code', '1000')->firstOrFail();

        $this->actingAs($this->user)->get('/finances/accounts')->assertStatus(200);
        $this->actingAs($this->user)->get('/finances/accounts/create')->assertStatus(200);
        $this->actingAs($this->user)->get(route('finances.accounts.edit', $account))->assertStatus(200);
    }

    public function test_account_can_be_created_and_updated()
    {
        $response = $this->actingAs($this->user)->post('/finances/accounts', [
            'code' => '1030',
            'name' => 'Petty Cash',
            'type' => Account::TYPE_ASSET,
            'description' => 'Small cash float',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('finances.accounts.index'));

        $account = Account::where('code', '1030')->firstOrFail();
        $this->assertSame('debit', $account->normal_balance);

        $this->actingAs($this->user)->put(route('finances.accounts.update', $account), [
            'code' => '1030',
            'name' => 'Petty Cash Fund',
            'type' => Account::TYPE_ASSET,
            'is_active' => 1,
        ])->assertRedirect(route('finances.accounts.index'));

        $this->assertDatabaseHas('accounts', ['id' => $account->id, 'name' => 'Petty Cash Fund']);
    }

    public function test_account_with_journal_lines_cannot_be_deleted()
    {
        $this->postEntry();

        $cash = Account::where('code', '1000')->firstOrFail();

        $response = $this->actingAs($this->user)->delete(route('finances.accounts.destroy', $cash));

        $response->assertRedirect(route('finances.accounts.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('accounts', ['id' => $cash->id]);
    }

    public function test_reports_are_accessible()
    {
        $this->postEntry();

        $cash = Account::where('code', '1000')->firstOrFail();

        $this->actingAs($this->user)->get('/finances/trial-balance')->assertStatus(200);
        $this->actingAs($this->user)->get('/finances/income-statement')->assertStatus(200);
        $this->actingAs($this->user)->get('/finances/balance-sheet')->assertStatus(200);
        $this->actingAs($this->user)->get(route('finances.ledger', $cash))->assertStatus(200);
    }

    public function test_trial_balance_totals_are_equal()
    {
        $this->postEntry(1500);

        $response = $this->actingAs($this->user)->get('/finances/trial-balance');

        $response->assertStatus(200);
        $response->assertViewHas('totalDebit', fn ($value) => abs($value - 1500) < 0.005);
        $response->assertViewHas('totalCredit', fn ($value) => abs($value - 1500) < 0.005);
    }

    public function test_income_statement_shows_net_surplus()
    {
        $this->postEntry(1500);

        $response = $this->actingAs($this->user)->get('/finances/income-statement');

        $response->assertStatus(200);
        $response->assertViewHas('totalIncome', fn ($value) => abs($value - 1500) < 0.005);
        $response->assertViewHas('totalExpense', fn ($value) => abs($value - 0) < 0.005);
        $response->assertViewHas('netIncome', fn ($value) => abs($value - 1500) < 0.005);
    }

    public function test_balance_sheet_balances_after_posted_entry()
    {
        $this->postEntry(1500);

        $response = $this->actingAs($this->user)->get('/finances/balance-sheet');

        $response->assertStatus(200);
        $response->assertViewHas('totalAssets', fn ($value) => abs($value - 1500) < 0.005);
        $response->assertViewHas('totalLiabilitiesAndEquity', fn ($value) => abs($value - 1500) < 0.005);
    }

    public function test_draft_entries_are_excluded_from_reports()
    {
        $this->postEntry(1500, JournalEntry::STATUS_DRAFT);

        $response = $this->actingAs($this->user)->get('/finances/income-statement');

        $response->assertStatus(200);
        $response->assertViewHas('totalIncome', fn ($value) => abs($value - 0) < 0.005);
    }

    public function test_account_balance_excludes_draft_entries()
    {
        $cash = Account::where('code', '1000')->firstOrFail();

        $this->postEntry(1500, JournalEntry::STATUS_DRAFT);

        $this->assertEquals(0, $cash->fresh()->balance);

        $this->postEntry(1500, JournalEntry::STATUS_POSTED);

        $this->assertEquals(1500, $cash->fresh()->balance);
    }

    public function test_journal_entry_update_is_audited_with_the_new_values()
    {
        $this->postEntry(1500, JournalEntry::STATUS_DRAFT);

        $entry = JournalEntry::firstOrFail();
        $cash = Account::where('code', '1000')->firstOrFail();
        $income = Account::where('code', '4000')->firstOrFail();

        $this->actingAs($this->user)->put(route('finances.update', $entry), [
            'entry_date' => now()->toDateString(),
            'reference' => 'RCPT-002',
            'description' => 'Updated donation',
            'status' => JournalEntry::STATUS_POSTED,
            'lines' => [
                ['account_id' => $cash->id, 'debit' => 2500, 'credit' => 0],
                ['account_id' => $income->id, 'debit' => 0, 'credit' => 2500],
            ],
        ])->assertRedirect(route('finances.show', $entry));

        $log = AuditLog::where('action', 'updated')->latest('id')->firstOrFail();

        $this->assertSame('RCPT-002', $log->new_values['reference']);
    }

    public function test_ledger_shows_closing_balance()
    {
        $this->postEntry(1500);

        $cash = Account::where('code', '1000')->firstOrFail();

        $response = $this->actingAs($this->user)->get(route('finances.ledger', $cash));

        $response->assertStatus(200);
        $response->assertViewHas('closing', fn ($value) => abs($value - 1500) < 0.005);
        $response->assertViewHas('totalDebit', fn ($value) => abs($value - 1500) < 0.005);
    }

    public function test_user_without_permission_cannot_access_finances()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/finances')->assertForbidden();
    }
}
