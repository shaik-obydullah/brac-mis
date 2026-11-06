<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Models\Branch;
use App\Services\BranchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Branch::with('parent')->withCount('children');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }
        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        return response()->json($query->paginate($request->get('per_page', 15)));
    }

    public function store(StoreBranchRequest $request): JsonResponse
    {
        $data = $request->validated();
        $parentId = $data['parent_id'] ?? null;

        app(BranchService::class)->validateHierarchy($data['type'], $parentId);

        $data['parent_id'] = $data['type'] === Branch::TYPE_HEAD_OFFICE ? null : $parentId;

        $branch = Branch::create($data);

        return response()->json($branch, 201);
    }

    public function show($id): JsonResponse
    {
        $branch = Branch::with('parent', 'children', 'staff', 'beneficiaries')->findOrFail($id);

        return response()->json($branch);
    }

    public function update(UpdateBranchRequest $request, $id): JsonResponse
    {
        $branch = Branch::findOrFail($id);
        $data = $request->validated();
        $type = $data['type'] ?? $branch->type;
        $parentId = array_key_exists('parent_id', $data) ? $data['parent_id'] : $branch->parent_id;

        $service = app(BranchService::class);
        $service->ensureTypeChangeAllowed($branch, $type);
        $service->validateHierarchy($type, $parentId, $branch->id);

        $data['parent_id'] = $type === Branch::TYPE_HEAD_OFFICE ? null : $parentId;

        $branch->update($data);

        return response()->json($branch);
    }

    public function destroy($id): JsonResponse
    {
        $branch = Branch::findOrFail($id);

        if ($branch->children()->exists()) {
            return response()->json(['message' => 'Cannot delete a branch that has child branches.'], 422);
        }

        $branch->delete();

        return response()->json(null, 204);
    }
}
