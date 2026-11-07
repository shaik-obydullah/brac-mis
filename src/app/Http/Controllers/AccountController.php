<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
    ) {}

    public function index(Request $request)
    {
        $query = Account::query()->withCount('lines');

        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type') && array_key_exists($request->type, Account::TYPES)) {
            $query->where('type', $request->type);
        }

        $accounts = $query
            ->withSum(['lines as posted_debit' => fn ($line) => $line->whereHas('entry', fn ($entry) => $entry->posted())], 'debit')
            ->withSum(['lines as posted_credit' => fn ($line) => $line->whereHas('entry', fn ($entry) => $entry->posted())], 'credit')
            ->orderBy('code')
            ->paginate(25)
            ->withQueryString();
        $types = Account::TYPES;

        return view('finances.accounts.index', compact('accounts', 'types'));
    }

    public function create()
    {
        $accounts = Account::orderBy('code')->get();
        $types = Account::TYPES;

        return view('finances.accounts.create', compact('accounts', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateAccount($request);
        $validated['normal_balance'] = Account::normalBalanceFor($validated['type']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $account = Account::create($validated);
        $this->auditService->logCreate($account);

        return redirect()->route('finances.accounts.index')
            ->with('success', 'Account created successfully.');
    }

    public function edit(Account $account)
    {
        $accounts = Account::whereKeyNot($account->id)->orderBy('code')->get();
        $types = Account::TYPES;

        return view('finances.accounts.edit', compact('account', 'accounts', 'types'));
    }

    public function update(Request $request, Account $account)
    {
        $validated = $this->validateAccount($request, $account);
        $validated['normal_balance'] = Account::normalBalanceFor($validated['type']);
        $validated['is_active'] = $request->boolean('is_active');

        $old = $account->toArray();
        $account->update($validated);
        $this->auditService->logUpdate($account, $old);

        return redirect()->route('finances.accounts.index')
            ->with('success', 'Account updated successfully.');
    }

    public function destroy(Account $account)
    {
        if ($account->lines()->exists()) {
            return redirect()->route('finances.accounts.index')
                ->with('error', 'This account cannot be deleted because it has journal entries. Deactivate it instead.');
        }

        $this->auditService->logDelete($account);
        $account->delete();

        return redirect()->route('finances.accounts.index')
            ->with('success', 'Account deleted successfully.');
    }

    private function validateAccount(Request $request, ?Account $account = null): array
    {
        return $request->validate([
            'code' => [
                'required', 'string', 'max:20',
                Rule::unique('accounts', 'code')->ignore($account?->id),
            ],
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(array_keys(Account::TYPES))],
            'parent_id' => 'nullable|exists:accounts,id',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ]);
    }
}
