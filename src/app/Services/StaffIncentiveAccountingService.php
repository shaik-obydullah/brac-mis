<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\StaffIncentive;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StaffIncentiveAccountingService
{
    /**
     * Expense account used for staff incentives.
     */
    public const EXPENSE_ACCOUNT = '5210';

    /**
     * Fallback expense accounts when the dedicated account is missing.
     */
    public const FALLBACK_EXPENSE_ACCOUNTS = ['5200', '5900'];

    /**
     * Account the incentive payment is credited to, keyed by paid_from value.
     */
    public const PAYMENT_ACCOUNTS = [
        'cash' => '1000',
        'bank' => '1010',
    ];

    /**
     * Create, update or remove the journal entry linked to a staff incentive.
     *
     * - status "paid"    -> posted journal entry (appears in reports)
     * - status "pending" -> draft journal entry (does not appear in reports)
     * - no amount        -> journal entry removed
     */
    public function sync(StaffIncentive $incentive): void
    {
        $amount = round((float) ($incentive->amount ?? 0), 2);

        if ($amount <= 0) {
            $this->remove($incentive);

            return;
        }

        $expenseAccount = $this->expenseAccount();
        $paymentAccount = $this->paymentAccount($incentive);

        if (! $expenseAccount || ! $paymentAccount) {
            throw new RuntimeException(
                'Cannot post staff incentive #'.$incentive->getKey().' because the required accounting accounts are missing.'
            );
        }

        DB::transaction(function () use ($incentive, $amount, $expenseAccount, $paymentAccount) {
            // Serialize syncs for the same record so two concurrent requests
            // cannot both create a journal entry for it.
            StaffIncentive::query()->whereKey($incentive->getKey())->lockForUpdate()->first();
            $incentive->refresh();

            $entry = $incentive->journalEntry()->first();

            $posting = $incentive->status === StaffIncentive::STATUS_PAID;

            $attributes = [
                'entry_date' => $incentive->incentive_date,
                'reference' => 'INC-'.$incentive->id,
                'description' => $this->description($incentive),
                'branch_id' => $incentive->staff?->branch_id,
                'status' => $posting ? JournalEntry::STATUS_POSTED : JournalEntry::STATUS_DRAFT,
                'created_by' => $incentive->created_by ?? auth()->id(),
                'posted_at' => $posting ? ($entry?->posted_at ?? now()) : null,
            ];

            if ($entry) {
                $entry->update($attributes);
                $entry->lines()->delete();
            } else {
                $entry = JournalEntry::createWithNextEntryNo($attributes);

                $incentive->journalEntry()->associate($entry);
                $incentive->saveQuietly();
            }

            $entry->lines()->createMany([
                [
                    'account_id' => $expenseAccount->id,
                    'description' => 'Staff incentive: '.$incentive->title,
                    'debit' => $amount,
                    'credit' => 0,
                ],
                [
                    'account_id' => $paymentAccount->id,
                    'description' => 'Payment for staff incentive: '.$incentive->title,
                    'debit' => 0,
                    'credit' => $amount,
                ],
            ]);
        });
    }

    /**
     * Remove the journal entry linked to a staff incentive, if any.
     */
    public function remove(StaffIncentive $incentive): void
    {
        DB::transaction(function () use ($incentive) {
            StaffIncentive::query()->whereKey($incentive->getKey())->lockForUpdate()->first();
            $incentive->refresh();

            if (! $incentive->journal_entry_id) {
                return;
            }

            $entry = $incentive->journalEntry()->first();

            $incentive->journalEntry()->dissociate();
            $incentive->saveQuietly();

            $entry?->delete();
        });
    }

    private function expenseAccount(): ?Account
    {
        foreach (array_merge([self::EXPENSE_ACCOUNT], self::FALLBACK_EXPENSE_ACCOUNTS) as $code) {
            $account = Account::where('code', $code)->first();

            if ($account) {
                return $account;
            }
        }

        return null;
    }

    private function paymentAccount(StaffIncentive $incentive): ?Account
    {
        $paidFrom = $incentive->paid_from === 'bank' ? 'bank' : 'cash';

        return Account::where('code', self::PAYMENT_ACCOUNTS[$paidFrom])->first()
            ?? Account::where('code', self::PAYMENT_ACCOUNTS['cash'])->first();
    }

    private function description(StaffIncentive $incentive): string
    {
        $staff = $incentive->staff;

        if (! $staff) {
            return 'Staff incentive: '.$incentive->title;
        }

        $name = $staff->user?->name ?? $staff->employee_id;

        return 'Staff incentive: '.$incentive->title.' for '.$name.' ('.$staff->employee_id.')';
    }
}
