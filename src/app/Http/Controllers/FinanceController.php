<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Finance;
use App\Services\AuditService;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Finance::with('branch');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
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

        $totals = [
            'income' => (clone $query)->where('type', 'income')->sum('amount'),
            'expense' => (clone $query)->where('type', 'expense')->sum('amount'),
        ];
        $totals['net'] = $totals['income'] - $totals['expense'];

        $finances = $query->latest('entry_date')->latest('id')->paginate(15);
        $branches = Branch::orderBy('name')->get();

        return view('finances.index', compact('finances', 'branches', 'totals'));
    }

    public function create()
    {
        $branches = Branch::orderBy('name')->get();

        return view('finances.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'type' => 'required|in:income,expense',
            'category' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'description' => 'nullable|string',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $validated['currency'] = $validated['currency'] ?? 'BDT';
        $validated['created_by'] = auth()->id();

        $finance = Finance::create($validated);
        app(AuditService::class)->logCreate($finance);

        return redirect('/finances')->with('success', 'Finance entry created successfully.');
    }

    public function show($id)
    {
        $finance = Finance::with('branch', 'createdBy')->findOrFail($id);

        return view('finances.show', compact('finance'));
    }

    public function edit($id)
    {
        $finance = Finance::findOrFail($id);
        $branches = Branch::orderBy('name')->get();

        return view('finances.edit', compact('finance', 'branches'));
    }

    public function update(Request $request, $id)
    {
        $finance = Finance::findOrFail($id);

        $validated = $request->validate([
            'entry_date' => 'required|date',
            'type' => 'required|in:income,expense',
            'category' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'description' => 'nullable|string',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $validated['currency'] = $validated['currency'] ?? 'BDT';

        $old = $finance->toArray();
        $finance->update($validated);
        app(AuditService::class)->logUpdate($finance, $old);

        return redirect('/finances')->with('success', 'Finance entry updated successfully.');
    }

    public function destroy($id)
    {
        $finance = Finance::findOrFail($id);
        app(AuditService::class)->logDelete($finance);
        $finance->delete();

        return redirect('/finances')->with('success', 'Finance entry deleted successfully.');
    }
}
