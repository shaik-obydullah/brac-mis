<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReturneeRequest;
use App\Http\Requests\UpdateReturneeRequest;
use App\Models\Returnee;
use App\Models\ReturneeFollowUp;
use App\Models\ReturneeLivelihoodSupport;
use App\Models\ReturneeReintegrationPlan;
use App\Models\ReturneeSkillAssessment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReturneeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Returnee::with('beneficiary', 'originCountry', 'migrant');

        if ($search = $request->get('status')) {
            $query->where('status', $search);
        }
        if ($beneficiaryId = $request->get('beneficiary_id')) {
            $query->where('beneficiary_id', $beneficiaryId);
        }
        if ($countryId = $request->get('origin_country_id')) {
            $query->where('origin_country_id', $countryId);
        }

        $perPage = $request->get('per_page', 15);

        return response()->json($query->paginate($perPage));
    }

    public function store(StoreReturneeRequest $request): JsonResponse
    {
        $returnee = Returnee::create($request->validated());
        $returnee->load('beneficiary', 'originCountry', 'migrant');

        return response()->json($returnee, 201);
    }

    public function show($id): JsonResponse
    {
        $returnee = Returnee::with([
            'beneficiary', 'originCountry', 'migrant',
            'reintegrationPlans', 'skillAssessments',
            'livelihoodSupport', 'microfinance', 'followUps',
        ])->findOrFail($id);

        return response()->json($returnee);
    }

    public function update(UpdateReturneeRequest $request, $id): JsonResponse
    {
        $returnee = Returnee::findOrFail($id);
        $returnee->update($request->validated());
        $returnee->load('beneficiary', 'originCountry', 'migrant');

        return response()->json($returnee);
    }

    public function destroy($id): JsonResponse
    {
        $returnee = Returnee::findOrFail($id);
        $returnee->delete();

        return response()->json(null, 204);
    }

    public function plans($id): JsonResponse
    {
        $returnee = Returnee::findOrFail($id);

        return response()->json($returnee->reintegrationPlans);
    }

    public function storePlan(Request $request, $id): JsonResponse
    {
        $returnee = Returnee::findOrFail($id);

        $validated = $request->validate([
            'staff_id' => 'nullable|exists:staff,id',
            'goal' => 'required|string',
            'activities' => 'nullable|string',
            'timeline' => 'nullable|string|max:255',
            'status' => 'sometimes|in:draft,in_progress,completed,cancelled',
        ]);

        $plan = $returnee->reintegrationPlans()->create($validated);

        return response()->json($plan, 201);
    }

    public function skillAssessments($id): JsonResponse
    {
        $returnee = Returnee::findOrFail($id);

        return response()->json($returnee->skillAssessments);
    }

    public function storeSkillAssessment(Request $request, $id): JsonResponse
    {
        $returnee = Returnee::findOrFail($id);

        $validated = $request->validate([
            'skill_name' => 'required|string|max:255',
            'proficiency_level' => 'nullable|string|max:100',
            'certification' => 'nullable|string|max:255',
            'assessed_by' => 'nullable|string|max:255',
            'assessed_date' => 'nullable|date',
        ]);

        $assessment = $returnee->skillAssessments()->create($validated);

        return response()->json($assessment, 201);
    }

    public function livelihoodSupport($id): JsonResponse
    {
        $returnee = Returnee::findOrFail($id);

        return response()->json($returnee->livelihoodSupport);
    }

    public function storeLivelihoodSupport(Request $request, $id): JsonResponse
    {
        $returnee = Returnee::findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'provider' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'sometimes|in:pending,active,completed,stopped',
        ]);

        $support = $returnee->livelihoodSupport()->create($validated);

        return response()->json($support, 201);
    }

    public function followUps($id): JsonResponse
    {
        $returnee = Returnee::findOrFail($id);

        return response()->json($returnee->followUps);
    }

    public function storeFollowUp(Request $request, $id): JsonResponse
    {
        $returnee = Returnee::findOrFail($id);

        $validated = $request->validate([
            'staff_id' => 'nullable|exists:staff,id',
            'type' => 'required|string|max:255',
            'date' => 'nullable|date',
            'notes' => 'nullable|string',
            'next_date' => 'nullable|date|after_or_equal:date',
            'status' => 'sometimes|in:scheduled,completed,cancelled',
        ]);

        $followUp = $returnee->followUps()->create($validated);

        return response()->json($followUp, 201);
    }
}
