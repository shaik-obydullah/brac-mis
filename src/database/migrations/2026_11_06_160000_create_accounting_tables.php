<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('type', 20);
            $table->string('normal_balance', 10);
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('type');
        });

        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_no', 30)->unique();
            $table->date('entry_date');
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20)->default('posted');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->index('entry_date');
            $table->index('status');
        });

        Schema::create('journal_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->restrictOnDelete();
            $table->string('description')->nullable();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->timestamps();

            $table->index('account_id');
        });

        $now = now();

        $accounts = [
            ['code' => '1000', 'name' => 'Cash in Hand', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1010', 'name' => 'Bank Account', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1100', 'name' => 'Accounts Receivable', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1200', 'name' => 'Prepaid Expenses', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1500', 'name' => 'Fixed Assets', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'liability', 'normal_balance' => 'credit'],
            ['code' => '2100', 'name' => 'Accrued Expenses', 'type' => 'liability', 'normal_balance' => 'credit'],
            ['code' => '2200', 'name' => 'Loans Payable', 'type' => 'liability', 'normal_balance' => 'credit'],
            ['code' => '3000', 'name' => 'Capital Fund', 'type' => 'equity', 'normal_balance' => 'credit'],
            ['code' => '3100', 'name' => 'Retained Earnings', 'type' => 'equity', 'normal_balance' => 'credit'],
            ['code' => '4000', 'name' => 'Donations & Grants', 'type' => 'income', 'normal_balance' => 'credit'],
            ['code' => '4100', 'name' => 'Program Service Income', 'type' => 'income', 'normal_balance' => 'credit'],
            ['code' => '4200', 'name' => 'Other Income', 'type' => 'income', 'normal_balance' => 'credit'],
            ['code' => '5000', 'name' => 'Reintegration Support', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '5100', 'name' => 'Immediate Support', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '5200', 'name' => 'Staff Salaries', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '5300', 'name' => 'Office & Administration', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '5400', 'name' => 'Training & Awareness', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '5500', 'name' => 'Travel & Transport', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '5600', 'name' => 'Utilities', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '5900', 'name' => 'Other Expenses', 'type' => 'expense', 'normal_balance' => 'debit'],
        ];

        DB::table('accounts')->insertOrIgnore(array_map(fn (array $account) => $account + [
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ], $accounts));

        $this->migrateLegacyFinanceEntries();
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('accounts');
    }

    private function migrateLegacyFinanceEntries(): void
    {
        if (! Schema::hasTable('finances')) {
            return;
        }

        $cashId = DB::table('accounts')->where('code', '1000')->value('id');
        $incomeId = DB::table('accounts')->where('code', '4200')->value('id');
        $expenseId = DB::table('accounts')->where('code', '5900')->value('id');

        if (! $cashId || ! $incomeId || ! $expenseId) {
            return;
        }

        foreach (DB::table('finances')->orderBy('id')->get() as $finance) {
            $now = now();

            $entryId = DB::table('journal_entries')->insertGetId([
                'entry_no' => 'LEGACY-'.$finance->id,
                'entry_date' => $finance->entry_date,
                'reference' => $finance->category,
                'description' => $finance->description ?: 'Migrated from finance entry #'.$finance->id,
                'branch_id' => $finance->branch_id,
                'status' => 'posted',
                'created_by' => $finance->created_by,
                'posted_at' => $finance->created_at ?? $now,
                'created_at' => $finance->created_at ?? $now,
                'updated_at' => $finance->updated_at ?? $now,
            ]);

            $isIncome = $finance->type === 'income';

            DB::table('journal_lines')->insert([
                [
                    'journal_entry_id' => $entryId,
                    'account_id' => $isIncome ? $cashId : $expenseId,
                    'description' => $finance->description,
                    'debit' => $finance->amount,
                    'credit' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'journal_entry_id' => $entryId,
                    'account_id' => $isIncome ? $incomeId : $cashId,
                    'description' => $finance->description,
                    'debit' => 0,
                    'credit' => $finance->amount,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);
        }
    }
};
