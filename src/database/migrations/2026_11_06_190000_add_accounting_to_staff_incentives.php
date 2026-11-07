<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const EXPENSE_ACCOUNT = [
        'code' => '5210',
        'name' => 'Staff Incentives',
        'type' => 'expense',
        'normal_balance' => 'debit',
    ];

    public function up(): void
    {
        if (! Schema::hasColumn('staff_incentives', 'paid_from')) {
            Schema::table('staff_incentives', function (Blueprint $table) {
                $table->string('paid_from', 20)->default('cash')->after('amount');
            });
        }

        if (! Schema::hasColumn('staff_incentives', 'journal_entry_id')) {
            Schema::table('staff_incentives', function (Blueprint $table) {
                $table->foreignId('journal_entry_id')
                    ->nullable()
                    ->after('created_by')
                    ->constrained('journal_entries')
                    ->nullOnDelete();
            });
        }

        $now = now();

        DB::table('accounts')->insertOrIgnore([self::EXPENSE_ACCOUNT + [
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('staff_incentives', 'journal_entry_id')) {
            Schema::table('staff_incentives', function (Blueprint $table) {
                $table->dropConstrainedForeignId('journal_entry_id');
            });
        }

        if (Schema::hasColumn('staff_incentives', 'paid_from')) {
            Schema::table('staff_incentives', function (Blueprint $table) {
                $table->dropColumn('paid_from');
            });
        }

        $code = self::EXPENSE_ACCOUNT['code'];

        $isUsed = DB::table('journal_lines')
            ->join('accounts', 'accounts.id', '=', 'journal_lines.account_id')
            ->where('accounts.code', $code)
            ->exists();

        if (! $isUsed) {
            DB::table('accounts')->where('code', $code)->delete();
        }
    }
};
