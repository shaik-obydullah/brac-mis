<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use Illuminate\Http\Request;

class FinanceReportController extends Controller
{
    public function ledger(Request $request, Account $account)
    {
        $from = $request->get('from') ?: now()->startOfYear()->toDateString();
        $to = $request->get('to') ?: now()->toDateString();

        $openingTotals = JournalLine::query()
            ->where('account_id', $account->id)
            ->whereHas('entry', fn ($query) => $query->posted()->whereDate('entry_date', '<', $from))
            ->selectRaw('COALESCE(SUM(debit), 0) as debit, COALESCE(SUM(credit), 0) as credit')
            ->first();

        $opening = $this->balanceFromTotals(
            (float) ($openingTotals->debit ?? 0),
            (float) ($openingTotals->credit ?? 0),
            $account->is_debit_normal
        );

        $lines = JournalLine::query()
            ->where('account_id', $account->id)
            ->whereHas('entry', fn ($query) => $query->posted()
                ->whereDate('entry_date', '>=', $from)
                ->whereDate('entry_date', '<=', $to))
            ->with('entry')
            ->get()
            ->sortBy(fn (JournalLine $line) => sprintf(
                '%s-%010d-%010d',
                $line->entry->entry_date->format('Y-m-d'),
                $line->journal_entry_id,
                $line->id
            ))
            ->values();

        $running = $opening;
        $rows = $lines->map(function (JournalLine $line) use (&$running, $account) {
            $running += $this->movement($line, $account->is_debit_normal);

            return [
                'line' => $line,
                'balance' => $running,
            ];
        });

        $totalDebit = (float) $lines->sum('debit');
        $totalCredit = (float) $lines->sum('credit');

        return view('finances.reports.ledger', [
            'account' => $account,
            'rows' => $rows,
            'opening' => $opening,
            'closing' => $running,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'from' => $from,
            'to' => $to,
            'accounts' => Account::active()->orderBy('code')->get(),
        ]);
    }

    public function trialBalance(Request $request)
    {
        $asOf = $request->get('as_of') ?: now()->toDateString();

        $totals = JournalLine::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->where('journal_entries.status', JournalEntry::STATUS_POSTED)
            ->whereDate('journal_entries.entry_date', '<=', $asOf)
            ->groupBy('journal_lines.account_id')
            ->selectRaw('journal_lines.account_id, COALESCE(SUM(journal_lines.debit), 0) as total_debit, COALESCE(SUM(journal_lines.credit), 0) as total_credit')
            ->get()
            ->keyBy('account_id');

        $accounts = Account::whereIn('id', $totals->keys())->orderBy('code')->get()->map(function (Account $account) use ($totals) {
            $row = $totals[$account->id];
            $debit = (float) $row->total_debit;
            $credit = (float) $row->total_credit;
            $balance = round($debit - $credit, 2);

            return [
                'account' => $account,
                'debit' => $debit,
                'credit' => $credit,
                'debit_balance' => $balance > 0 ? $balance : 0,
                'credit_balance' => $balance < 0 ? abs($balance) : 0,
            ];
        });

        return view('finances.reports.trial-balance', [
            'accounts' => $accounts,
            'asOf' => $asOf,
            'totalDebit' => (float) $accounts->sum('debit_balance'),
            'totalCredit' => (float) $accounts->sum('credit_balance'),
        ]);
    }

    public function incomeStatement(Request $request)
    {
        $from = $request->get('from') ?: now()->startOfYear()->toDateString();
        $to = $request->get('to') ?: now()->toDateString();

        $totals = JournalLine::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->where('journal_entries.status', JournalEntry::STATUS_POSTED)
            ->whereDate('journal_entries.entry_date', '>=', $from)
            ->whereDate('journal_entries.entry_date', '<=', $to)
            ->groupBy('journal_lines.account_id')
            ->selectRaw('journal_lines.account_id, COALESCE(SUM(journal_lines.debit), 0) as total_debit, COALESCE(SUM(journal_lines.credit), 0) as total_credit')
            ->get()
            ->keyBy('account_id');

        $accounts = Account::whereIn('type', [Account::TYPE_INCOME, Account::TYPE_EXPENSE])
            ->whereIn('id', $totals->keys())
            ->orderBy('code')
            ->get()
            ->map(function (Account $account) use ($totals) {
                $row = $totals[$account->id];
                $debit = (float) $row->total_debit;
                $credit = (float) $row->total_credit;

                return [
                    'account' => $account,
                    'amount' => $account->is_debit_normal ? $debit - $credit : $credit - $debit,
                ];
            })
            ->filter(fn (array $row) => abs($row['amount']) >= 0.005)
            ->values();

        $income = $accounts->where('account.type', Account::TYPE_INCOME);
        $expenses = $accounts->where('account.type', Account::TYPE_EXPENSE);
        $totalIncome = (float) $income->sum('amount');
        $totalExpense = (float) $expenses->sum('amount');

        return view('finances.reports.income-statement', [
            'income' => $income,
            'expenses' => $expenses,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netIncome' => $totalIncome - $totalExpense,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function balanceSheet(Request $request)
    {
        $asOf = $request->get('as_of') ?: now()->toDateString();

        $totals = JournalLine::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->where('journal_entries.status', JournalEntry::STATUS_POSTED)
            ->whereDate('journal_entries.entry_date', '<=', $asOf)
            ->groupBy('journal_lines.account_id')
            ->selectRaw('journal_lines.account_id, COALESCE(SUM(journal_lines.debit), 0) as total_debit, COALESCE(SUM(journal_lines.credit), 0) as total_credit')
            ->get()
            ->keyBy('account_id');

        $sections = collect([
            Account::TYPE_ASSET => collect(),
            Account::TYPE_LIABILITY => collect(),
            Account::TYPE_EQUITY => collect(),
            Account::TYPE_INCOME => collect(),
            Account::TYPE_EXPENSE => collect(),
        ]);

        foreach ($totals as $accountId => $row) {
            $account = Account::find($accountId);

            if (! $account) {
                continue;
            }

            $debit = (float) $row->total_debit;
            $credit = (float) $row->total_credit;
            $amount = $account->is_debit_normal ? $debit - $credit : $credit - $debit;

            $sections[$account->type]->push([
                'account' => $account,
                'amount' => $amount,
            ]);
        }

        $sections = $sections->map(fn ($rows) => $rows->sortBy('account.code')->values());

        $totalAssets = (float) $sections[Account::TYPE_ASSET]->sum('amount');
        $totalLiabilities = (float) $sections[Account::TYPE_LIABILITY]->sum('amount');
        $totalEquity = (float) $sections[Account::TYPE_EQUITY]->sum('amount');
        $netIncome = (float) $sections[Account::TYPE_INCOME]->sum('amount')
            - (float) $sections[Account::TYPE_EXPENSE]->sum('amount');

        return view('finances.reports.balance-sheet', [
            'assets' => $sections[Account::TYPE_ASSET],
            'liabilities' => $sections[Account::TYPE_LIABILITY],
            'equity' => $sections[Account::TYPE_EQUITY],
            'totalAssets' => $totalAssets,
            'totalLiabilities' => $totalLiabilities,
            'totalEquity' => $totalEquity,
            'netIncome' => $netIncome,
            'totalLiabilitiesAndEquity' => $totalLiabilities + $totalEquity + $netIncome,
            'asOf' => $asOf,
        ]);
    }

    private function balanceFromTotals(float $debit, float $credit, bool $debitNormal): float
    {
        return $debitNormal ? $debit - $credit : $credit - $debit;
    }

    private function movement(JournalLine $line, bool $debitNormal): float
    {
        $debit = (float) $line->debit;
        $credit = (float) $line->credit;

        return $debitNormal ? $debit - $credit : $credit - $debit;
    }
}
