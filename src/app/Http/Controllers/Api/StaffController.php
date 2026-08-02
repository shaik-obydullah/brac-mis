<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Staff::with('user', 'branch');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('employee_id', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%");
            });
        }
        if ($branchId = $request->get('branch_id')) {
            $query->where('branch_id', $branchId);
        }

        return response()->json($query->paginate($request->get('per_page', 15)));
    }

    public function store(StoreStaffRequest $request): JsonResponse
    {
        $staff = Staff::create($request->validated());
        $staff->load('user', 'branch');

        return response()->json($staff, 201);
    }

    public function show($id): JsonResponse
    {
        $staff = Staff::with('user', 'branch')->findOrFail($id);

        return response()->json($staff);
    }

    public function update(UpdateStaffRequest $request, $id): JsonResponse
    {
        $staff = Staff::findOrFail($id);
        $staff->update($request->validated());
        $staff->load('user', 'branch');

        return response()->json($staff);
    }

    public function destroy($id): JsonResponse
    {
        $staff = Staff::findOrFail($id);
        $staff->delete();

        return response()->json(null, 204);
    }
}
