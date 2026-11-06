<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Branch;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FinanceController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
    ) {}

    public function index(Request $request)
    {
        $query = JournalEntry::with(['branch', 'createdBy'])->withCount('lines');

        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('entry_no', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && array_key_exists($request->status, JournalEntry::STATUSES)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('from')) {
            $query->whereDate('entry_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('entry_date', '<=', $request->to);
        }

        $entryIds = (clone $query)->select('id');

        $totals = [
            'entries' => (clone $query)->count(),
            'debit' => (float) JournalLine::whereIn('journal_entry_id', $entryIds)->sum('debit'),
            'credit' => (float) JournalLine::whereIn('journal_entry_id', $entryIds)->sum('credit'),
            'draft' => (clone $query)->where('status', JournalEntry::STATUS_DRAFT)->count(),
        ];

        $entries = $query->latest('entry_date')->latest('id')->paginate(15)->withQueryString();
        $branches = Branch::orderBy('name')->get();
        $statuses = JournalEntry::STATUSES;

        return view('finances.index', compact('entries', 'branches', 'totals', 'statuses'));
    }

    public function create()
    {
        $accounts = Account::active()->orderBy('code')->get();
        $branches = Branch::orderBy('name')->get();
        $statuses = JournalEntry::STATUSES;

        return view('finances.create', compact('accounts', 'branches', 'statuses'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateEntry($request);

        $entry = DB::transaction(function () use ($validated) {
            $entry = JournalEntry::create([
                'entry_no' => JournalEntry::nextEntryNo($validated['entry_date']),
                'entry_date' => $validated['entry_date'],
                'reference' => $validated['reference'] ?? null,
                'description' => $validated['description'] ?? null,
                'branch_id' => $validated['branch_id'] ?? null,
                'status' => $validated['status'],
                'created_by' => auth()->id(),
                'posted_at' => $validated['status'] === JournalEntry::STATUS_POSTED ? now() : null,
            ]);

            $entry->lines()->createMany($validated['lines']);

            return $entry;
        });

        $this->auditService->logCreate($entry);

        return redirect()->route('finances.show', $entry)
            ->with('success', 'Journal entry created successfully.');
    }

    public function show(JournalEntry $journalEntry)
    {
        $journalEntry->load('lines.account', 'branch', 'createdBy');

        return view('finances.show', ['entry' => $journalEntry]);
    }

    public function edit(JournalEntry $journalEntry)
    {
        $journalEntry->load('lines');
        $accounts = Account::active()->orderBy('code')->get();
        $branches = Branch::orderBy('name')->get();
        $statuses = JournalEntry::STATUSES;

        return view('finances.edit', compact('journalEntry', 'accounts', 'branches', 'statuses'));
    }

    public function update(Request $request, JournalEntry $journalEntry)
    {
        $validated = $this->validateEntry($request);

        DB::transaction(function () use ($validated, $journalEntry) {
            $old = $journalEntry->load('lines')->toArray();

            $journalEntry->update([
                'entry_date' => $validated['entry_date'],
                'reference' => $validated['reference'] ?? null,
                'description' => $validated['description'] ?? null,
                'branch_id' => $validated['branch_id'] ?? null,
                'status' => $validated['status'],
                'posted_at' => $validated['status'] === JournalEntry::STATUS_POSTED
                    ? ($journalEntry->posted_at ?? now())
                    : null,
            ]);

            $journalEntry->lines()->delete();
            $journalEntry->lines()->createMany($validated['lines']);

            $this->auditService->logUpdate($journalEntry->fresh('lines'), $old);
        });

        return redirect()->route('finances.show', $journalEntry)
            ->with('success', 'Journal entry updated successfully.');
    }

    public function destroy(JournalEntry $journalEntry)
    {
        $this->auditService->logDelete($journalEntry);
        $journalEntry->delete();

        return redirect()->route('finances.index')
            ->with('success', 'Journal entry deleted successfully.');
    }

    private function validateEntry(Request $request): array
    {
        $data = $request->validate([
            'entry_date' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'branch_id' => 'nullable|exists:branches,id',
            'status' => ['required', Rule::in(array_keys(JournalEntry::STATUSES))],
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.description' => 'nullable|string|max:255',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
        ]);

        $lines = collect($data['lines'])
            ->map(function ($line) {
                return [
                    'account_id' => (int) $line['account_id'],
                    'description' => $line['description'] ?? null,
                    'debit' => round((float) ($line['debit'] ?? 0), 2),
                    'credit' => round((float) ($line['credit'] ?? 0), 2),
                ];
            })
            ->filter(fn ($line) => $line['debit'] > 0 || $line['credit'] > 0)
            ->values();

        if ($lines->count() < 2) {
            throw ValidationException::withMessages([
                'lines' => 'Add at least two lines with a debit or credit amount.',
            ]);
        }

        if ($lines->contains(fn ($line) => $line['debit'] > 0 && $line['credit'] > 0)) {
            throw ValidationException::withMessages([
                'lines' => 'Each line can be either a debit or a credit, not both.',
            ]);
        }

        $totalDebit = round($lines->sum('debit'), 2);
        $totalCredit = round($lines->sum('credit'), 2);

        if ($totalDebit <= 0) {
            throw ValidationException::withMessages([
                'lines' => 'The journal entry must contain a debit amount.',
            ]);
        }

        if (abs($totalDebit - $totalCredit) >= 0.005) {
            throw ValidationException::withMessages([
                'lines' => 'Total debits ('.number_format($totalDebit, 2).') must equal total credits ('.number_format($totalCredit, 2).').',
            ]);
        }

        $data['lines'] = $lines->all();

        return $data;
    }
}
