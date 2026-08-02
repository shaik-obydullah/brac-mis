<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Models\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Branch::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        return response()->json($query->paginate($request->get('per_page', 15)));
    }

    public function store(StoreBranchRequest $request): JsonResponse
    {
        $branch = Branch::create($request->validated());

        return response()->json($branch, 201);
    }

    public function show($id): JsonResponse
    {
        $branch = Branch::with('staff', 'beneficiaries')->findOrFail($id);

        return response()->json($branch);
    }

    public function update(UpdateBranchRequest $request, $id): JsonResponse
    {
        $branch = Branch::findOrFail($id);
        $branch->update($request->validated());

        return response()->json($branch);
    }

    public function destroy($id): JsonResponse
    {
        $branch = Branch::findOrFail($id);
        $branch->delete();

        return response()->json(null, 204);
    }
}
