<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Branch;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    public function index(Request $request)
    {
        $query = Beneficiary::with('branch');

        if ($name = $request->get('name')) {
            $query->where('name', 'like', "%{$name}%");
        }

        if ($bracId = $request->get('brac_id')) {
            $query->where('brac_id', 'like', "%{$bracId}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $beneficiaries = $query->latest()->paginate(15);
        $branches = Branch::all();

        return view('beneficiaries.index', compact('beneficiaries', 'branches'));
    }

    public function create()
    {
        $branches = Branch::all();
        $staff = User::all();

        return view('beneficiaries.create', compact('branches', 'staff'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'brac_id' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'nid_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'family_size' => 'nullable|integer|min:0',
            'status' => 'nullable|string|max:50',
            'created_by' => 'nullable|exists:users,id',
        ]);

        $validated['created_by'] ??= auth()->id();
        $beneficiary = Beneficiary::create($validated);
        app(AuditService::class)->logCreate($beneficiary);

        return redirect('/beneficiaries')->with('success', 'Beneficiary created successfully.');
    }

    public function show($id)
    {
        $beneficiary = Beneficiary::with([
            'branch', 'createdBy', 'households', 'interventions', 'followUps', 'documents', 'migrants', 'returnees',
        ])->findOrFail($id);

        return view('beneficiaries.show', compact('beneficiary'));
    }

    public function edit($id)
    {
        $beneficiary = Beneficiary::findOrFail($id);
        $branches = Branch::all();
        $staff = User::all();

        return view('beneficiaries.edit', compact('beneficiary', 'branches', 'staff'));
    }

    public function update(Request $request, $id)
    {
        $beneficiary = Beneficiary::findOrFail($id);

        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'brac_id' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'nid_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'family_size' => 'nullable|integer|min:0',
            'status' => 'nullable|string|max:50',
            'created_by' => 'nullable|exists:users,id',
        ]);

        $old = $beneficiary->toArray();
        $beneficiary->update($validated);
        app(AuditService::class)->logUpdate($beneficiary, $old);

        return redirect('/beneficiaries')->with('success', 'Beneficiary updated successfully.');
    }

    public function destroy($id)
    {
        $beneficiary = Beneficiary::findOrFail($id);
        app(AuditService::class)->logDelete($beneficiary);
        $beneficiary->delete();

        return redirect('/beneficiaries')->with('success', 'Beneficiary deleted successfully.');
    }
}
