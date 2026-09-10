<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Staff;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = Staff::with('user', 'branch');

        if ($name = $request->get('name')) {
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$name}%"));
        }

        if ($employeeId = $request->get('employee_id')) {
            $query->where('employee_id', 'like', "%{$employeeId}%");
        }

        if ($designation = $request->get('designation')) {
            $query->where('designation', 'like', "%{$designation}%");
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $staff = $query->latest()->paginate(15);
        $branches = Branch::all();

        return view('staff.index', compact('staff', 'branches'));
    }

    public function create()
    {
        $users = User::all();
        $branches = Branch::all();

        return view('staff.create', compact('users', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'employee_id' => 'nullable|string|max:50|unique:staff,employee_id',
            'designation' => 'nullable|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'phone' => 'nullable|string|max:20',
        ]);

        $staff = Staff::create($validated);
        app(AuditService::class)->logCreate($staff);

        return redirect('/staff')->with('success', 'Staff created successfully.');
    }

    public function show($id)
    {
        $staff = Staff::with('user', 'branch')->findOrFail($id);

        return view('staff.show', compact('staff'));
    }

    public function edit($id)
    {
        $staff = Staff::findOrFail($id);
        $users = User::all();
        $branches = Branch::all();

        return view('staff.edit', compact('staff', 'users', 'branches'));
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'employee_id' => 'nullable|string|max:50|unique:staff,employee_id,' . $staff->id,
            'designation' => 'nullable|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'phone' => 'nullable|string|max:20',
        ]);

        $old = $staff->toArray();
        $staff->update($validated);
        app(AuditService::class)->logUpdate($staff, $old);

        return redirect('/staff')->with('success', 'Staff updated successfully.');
    }

    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        app(AuditService::class)->logDelete($staff);
        $staff->delete();

        return redirect('/staff')->with('success', 'Staff deleted successfully.');
    }
}
