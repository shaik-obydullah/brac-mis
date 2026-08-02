<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBeneficiaryRequest;
use App\Http\Requests\UpdateBeneficiaryRequest;
use App\Models\Beneficiary;
use App\Models\BeneficiaryFollowUp;
use App\Models\BeneficiaryHousehold;
use App\Models\BeneficiaryIntervention;
use App\Services\BeneficiaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    public function __construct(
        protected BeneficiaryService $beneficiaryService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Beneficiary::with('branch', 'createdBy');

        if ($search = $request->get('name')) {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($bracId = $request->get('brac_id')) {
            $query->where('brac_id', 'like', "%{$bracId}%");
        }
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($branchId = $request->get('branch_id')) {
            $query->where('branch_id', $branchId);
        }
        if ($phone = $request->get('phone')) {
            $query->where('phone', 'like', "%{$phone}%");
        }

        $perPage = $request->get('per_page', 15);

        return response()->json($query->paginate($perPage));
    }

    public function store(StoreBeneficiaryRequest $request): JsonResponse
    {
        $beneficiary = $this->beneficiaryService->registerBeneficiary($request->validated());

        $beneficiary->load('branch', 'createdBy');

        return response()->json($beneficiary, 201);
    }

    public function show($id): JsonResponse
    {
        $beneficiary = Beneficiary::with([
            'branch', 'createdBy', 'households', 'interventions',
            'followUps', 'documents', 'migrants', 'returnees',
        ])->findOrFail($id);

        return response()->json($beneficiary);
    }

    public function update(UpdateBeneficiaryRequest $request, $id): JsonResponse
    {
        $beneficiary = Beneficiary::findOrFail($id);
        $beneficiary->update($request->validated());

        $beneficiary->load('branch', 'createdBy');

        return response()->json($beneficiary);
    }

    public function destroy($id): JsonResponse
    {
        $beneficiary = Beneficiary::findOrFail($id);
        $beneficiary->delete();

        return response()->json(null, 204);
    }

    public function households($id): JsonResponse
    {
        $beneficiary = Beneficiary::findOrFail($id);

        return response()->json($beneficiary->households);
    }

    public function storeHousehold(Request $request, $id): JsonResponse
    {
        $beneficiary = Beneficiary::findOrFail($id);

        $validated = $request->validate([
            'member_name' => 'required|string|max:255',
            'relationship' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:0',
            'occupation' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
        ]);

        $household = $beneficiary->households()->create($validated);

        return response()->json($household, 201);
    }

    public function interventions($id): JsonResponse
    {
        $beneficiary = Beneficiary::findOrFail($id);

        return response()->json($beneficiary->interventions);
    }

    public function storeIntervention(Request $request, $id): JsonResponse
    {
        $beneficiary = Beneficiary::findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'sometimes|in:planned,ongoing,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = $request->user()->id;

        $intervention = $beneficiary->interventions()->create($validated);

        return response()->json($intervention, 201);
    }

    public function followUps($id): JsonResponse
    {
        $beneficiary = Beneficiary::findOrFail($id);

        return response()->json($beneficiary->followUps);
    }

    public function storeFollowUp(Request $request, $id): JsonResponse
    {
        $beneficiary = Beneficiary::findOrFail($id);

        $validated = $request->validate([
            'staff_id' => 'nullable|exists:staff,id',
            'type' => 'required|string|max:255',
            'date' => 'nullable|date',
            'notes' => 'nullable|string',
            'next_date' => 'nullable|date|after_or_equal:date',
            'status' => 'sometimes|in:scheduled,completed,cancelled',
        ]);

        $followUp = $beneficiary->followUps()->create($validated);

        return response()->json($followUp, 201);
    }
}
