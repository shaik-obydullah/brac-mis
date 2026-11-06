<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Expense account used for each support category.
     */
    private const EXPENSE_ACCOUNTS = [
        ['code' => '5110', 'name' => 'Mental & Psychological Support', 'type' => 'expense', 'normal_balance' => 'debit'],
        ['code' => '5120', 'name' => 'Physical Support', 'type' => 'expense', 'normal_balance' => 'debit'],
        ['code' => '5130', 'name' => 'Financial Support', 'type' => 'expense', 'normal_balance' => 'debit'],
    ];

    public function up(): void
    {
        if (! Schema::hasColumn('immediate_supports', 'journal_entry_id')) {
            Schema::table('immediate_supports', function (Blueprint $table) {
                $table->foreignId('journal_entry_id')
                    ->nullable()
                    ->after('provided_by')
                    ->constrained('journal_entries')
                    ->nullOnDelete();
            });
        }

        $now = now();

        DB::table('accounts')->insertOrIgnore(array_map(fn (array $account) => $account + [
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ], self::EXPENSE_ACCOUNTS));
    }

    public function down(): void
    {
        if (Schema::hasColumn('immediate_supports', 'journal_entry_id')) {
            Schema::table('immediate_supports', function (Blueprint $table) {
                $table->dropConstrainedForeignId('journal_entry_id');
            });
        }

        $codes = array_column(self::EXPENSE_ACCOUNTS, 'code');

        $usedCodes = DB::table('journal_lines')
            ->join('accounts', 'accounts.id', '=', 'journal_lines.account_id')
            ->whereIn('accounts.code', $codes)
            ->pluck('accounts.code')
            ->all();

        DB::table('accounts')
            ->whereIn('code', array_diff($codes, $usedCodes))
            ->delete();
    }
};
