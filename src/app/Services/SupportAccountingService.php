<?php

namespace App\Services;

use App\Models\Account;
use App\Models\ImmediateSupport;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SupportAccountingService
{
    /**
     * Expense account code for each support category.
     */
    public const EXPENSE_ACCOUNTS = [
        'immediate' => '5100',
        'mental-psychological' => '5110',
        'physical' => '5120',
        'financial' => '5130',
    ];

    public const DEFAULT_EXPENSE_ACCOUNT = '5900';

    /**
     * Account the support payment is credited to, keyed by paid_from value.
     */
    public const PAYMENT_ACCOUNTS = [
        'cash' => '1000',
        'bank' => '1010',
    ];

    /**
     * Create, update or remove the journal entry linked to a support record.
     *
     * - status "provided"  -> posted journal entry (appears in reports)
     * - status "pending"   -> draft journal entry (does not appear in reports)
     * - status "cancelled" -> journal entry removed
     * - no amount          -> journal entry removed
     */
    public function sync(ImmediateSupport $support): void
    {
        $amount = round((float) ($support->amount ?? 0), 2);

        if ($amount <= 0 || $support->status === 'cancelled') {
            $this->remove($support);

            return;
        }

        $expenseAccount = $this->expenseAccountFor($support);
        $paymentAccount = $this->paymentAccount($support);

        if (! $expenseAccount || ! $paymentAccount) {
            throw new RuntimeException(
                'Cannot post support #'.$support->getKey().' because the required accounting accounts are missing.'
            );
        }

        DB::transaction(function () use ($support, $amount, $expenseAccount, $paymentAccount) {
            // Serialize syncs for the same record so two concurrent requests
            // cannot both create a journal entry for it.
            ImmediateSupport::query()->whereKey($support->getKey())->lockForUpdate()->first();
            $support->refresh();

            $entry = $support->journalEntry()->first();

            $posting = $support->status === 'provided';

            $attributes = [
                'entry_date' => $support->support_date,
                'reference' => 'SUP-'.$support->id,
                'description' => $this->description($support),
                'branch_id' => null,
                'status' => $posting ? JournalEntry::STATUS_POSTED : JournalEntry::STATUS_DRAFT,
                'created_by' => $support->provided_by ?? auth()->id(),
                'posted_at' => $posting ? ($entry?->posted_at ?? now()) : null,
            ];

            if ($entry) {
                $entry->update($attributes);
                $entry->lines()->delete();
            } else {
                $entry = JournalEntry::createWithNextEntryNo($attributes);

                $support->journalEntry()->associate($entry);
                $support->saveQuietly();
            }

            $entry->lines()->createMany([
                [
                    'account_id' => $expenseAccount->id,
                    'description' => $support->type_label.' support',
                    'debit' => $amount,
                    'credit' => 0,
                ],
                [
                    'account_id' => $paymentAccount->id,
                    'description' => 'Payment for '.$support->type_label.' support',
                    'debit' => 0,
                    'credit' => $amount,
                ],
            ]);
        });
    }

    /**
     * Remove the journal entry linked to a support record, if any.
     */
    public function remove(ImmediateSupport $support): void
    {
        DB::transaction(function () use ($support) {
            ImmediateSupport::query()->whereKey($support->getKey())->lockForUpdate()->first();
            $support->refresh();

            if (! $support->journal_entry_id) {
                return;
            }

            $entry = $support->journalEntry()->first();

            $support->journalEntry()->dissociate();
            $support->saveQuietly();

            $entry?->delete();
        });
    }

    public function expenseAccountFor(ImmediateSupport $support): ?Account
    {
        $category = ImmediateSupport::categoryForType($support->support_type);
        $code = self::EXPENSE_ACCOUNTS[$category] ?? self::DEFAULT_EXPENSE_ACCOUNT;

        return Account::where('code', $code)->first();
    }

    private function paymentAccount(ImmediateSupport $support): ?Account
    {
        $paidFrom = $support->paid_from === 'bank' ? 'bank' : 'cash';

        return Account::where('code', self::PAYMENT_ACCOUNTS[$paidFrom])->first()
            ?? Account::where('code', self::PAYMENT_ACCOUNTS['cash'])->first();
    }

    private function description(ImmediateSupport $support): string
    {
        return $support->type_label.' support for '.$support->recipient_display_name;
    }
}
