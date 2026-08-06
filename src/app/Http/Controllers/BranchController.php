<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Services\AuditService;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $query = Branch::query();

        if ($name = $request->get('name')) {
            $query->where('name', 'like', "%{$name}%");
        }

        if ($code = $request->get('code')) {
            $query->where('code', 'like', "%{$code}%");
        }

        if ($district = $request->get('district')) {
            $query->where('district', 'like', "%{$district}%");
        }

        if ($division = $request->get('division')) {
            $query->where('division', 'like', "%{$division}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $branches = $query->latest()->paginate(15);

        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        return view('branches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:branches,code',
            'district' => 'nullable|string|max:255',
            'division' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        $branch = Branch::create($validated);
        app(AuditService::class)->logCreate($branch);

        return redirect('/branches')->with('success', 'Branch created successfully.');
    }

    public function show($id)
    {
        $branch = Branch::with('staff', 'beneficiaries')->findOrFail($id);

        return view('branches.show', compact('branch'));
    }

    public function edit($id)
    {
        $branch = Branch::findOrFail($id);

        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:branches,code,' . $branch->id,
            'district' => 'nullable|string|max:255',
            'division' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        $old = $branch->toArray();
        $branch->update($validated);
        app(AuditService::class)->logUpdate($branch, $old);

        return redirect('/branches')->with('success', 'Branch updated successfully.');
    }

    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);
        app(AuditService::class)->logDelete($branch);
        $branch->delete();

        return redirect('/branches')->with('success', 'Branch deleted successfully.');
    }
}
