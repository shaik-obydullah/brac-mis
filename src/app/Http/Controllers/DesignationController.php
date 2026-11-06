<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\Staff;
use App\Services\AuditService;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function index(Request $request)
    {
        $query = Designation::query();

        if ($name = $request->get('name')) {
            $query->where('name', 'like', "%{$name}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $designations = $query->orderBy('name')->paginate(15);

        return view('designations.index', compact('designations'));
    }

    public function create()
    {
        return view('designations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:designations,name',
            'status' => 'nullable|boolean',
        ]);

        $designation = Designation::create($validated);
        app(AuditService::class)->logCreate($designation);

        return redirect('/designations')->with('success', 'Designation created successfully.');
    }

    public function edit($id)
    {
        $designation = Designation::findOrFail($id);

        return view('designations.edit', compact('designation'));
    }

    public function update(Request $request, $id)
    {
        $designation = Designation::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:designations,name,'.$designation->id,
            'status' => 'nullable|boolean',
        ]);

        $old = $designation->toArray();
        $designation->update($validated);
        app(AuditService::class)->logUpdate($designation, $old);

        return redirect('/designations')->with('success', 'Designation updated successfully.');
    }

    public function destroy($id)
    {
        $designation = Designation::findOrFail($id);

        if (Staff::where('designation', $designation->name)->exists()) {
            return redirect('/designations')->with('error', 'Cannot delete a designation that is assigned to staff.');
        }

        app(AuditService::class)->logDelete($designation);
        $designation->delete();

        return redirect('/designations')->with('success', 'Designation deleted successfully.');
    }
}
