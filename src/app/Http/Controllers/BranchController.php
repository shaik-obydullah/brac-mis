<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Services\AuditService;
use App\Services\BranchService;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $query = Branch::with('parent')->withCount('children');

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

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $branches = $query->orderBy('type')->orderBy('name')->paginate(15);

        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        $headOffices = Branch::where('type', Branch::TYPE_HEAD_OFFICE)->orderBy('name')->get();
        $districts = Branch::where('type', Branch::TYPE_DISTRICT)->orderBy('name')->get();

        return view('branches.create', compact('headOffices', 'districts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:branches,code',
            'type' => 'required|in:head_office,district,upazila',
            'parent_id' => 'nullable|exists:branches,id',
            'division' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'upazila' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        $parentId = $validated['parent_id'] ?? null;
        app(BranchService::class)->validateHierarchy($validated['type'], $parentId);

        $validated['parent_id'] = $validated['type'] === Branch::TYPE_HEAD_OFFICE ? null : $parentId;

        $branch = Branch::create($validated);
        app(AuditService::class)->logCreate($branch);

        return redirect('/branches')->with('success', 'Branch created successfully.');
    }

    public function show($id)
    {
        $branch = Branch::with('parent', 'children', 'staff', 'beneficiaries')->findOrFail($id);

        return view('branches.show', compact('branch'));
    }

    public function edit($id)
    {
        $branch = Branch::findOrFail($id);
        $headOffices = Branch::where('type', Branch::TYPE_HEAD_OFFICE)->where('id', '!=', $branch->id)->orderBy('name')->get();
        $districts = Branch::where('type', Branch::TYPE_DISTRICT)->where('id', '!=', $branch->id)->orderBy('name')->get();

        return view('branches.edit', compact('branch', 'headOffices', 'districts'));
    }

    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:branches,code,'.$branch->id,
            'type' => 'required|in:head_office,district,upazila',
            'parent_id' => 'nullable|exists:branches,id',
            'division' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'upazila' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        $parentId = $validated['parent_id'] ?? null;
        $service = app(BranchService::class);
        $service->ensureTypeChangeAllowed($branch, $validated['type']);
        $service->validateHierarchy($validated['type'], $parentId, $branch->id);

        $validated['parent_id'] = $validated['type'] === Branch::TYPE_HEAD_OFFICE ? null : $parentId;

        $old = $branch->toArray();
        $branch->update($validated);
        app(AuditService::class)->logUpdate($branch, $old);

        return redirect('/branches')->with('success', 'Branch updated successfully.');
    }

    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);

        if ($branch->children()->exists()) {
            return redirect('/branches')->with('error', 'Cannot delete a branch that has child branches.');
        }

        app(AuditService::class)->logDelete($branch);
        $branch->delete();

        return redirect('/branches')->with('success', 'Branch deleted successfully.');
    }
}
