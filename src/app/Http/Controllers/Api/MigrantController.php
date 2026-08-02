<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMigrantRequest;
use App\Http\Requests\UpdateMigrantRequest;
use App\Models\Migrant;
use App\Models\MigrantDestination;
use App\Models\MigrantDocument;
use App\Models\MigrantFinancialRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MigrantController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Migrant::with('beneficiary', 'destinationCountry');

        if ($search = $request->get('status')) {
            $query->where('status', $search);
        }
        if ($beneficiaryId = $request->get('beneficiary_id')) {
            $query->where('beneficiary_id', $beneficiaryId);
        }
        if ($countryId = $request->get('destination_country_id')) {
            $query->where('destination_country_id', $countryId);
        }

        $perPage = $request->get('per_page', 15);

        return response()->json($query->paginate($perPage));
    }

    public function store(StoreMigrantRequest $request): JsonResponse
    {
        $migrant = Migrant::create($request->validated());
        $migrant->load('beneficiary', 'destinationCountry');

        return response()->json($migrant, 201);
    }

    public function show($id): JsonResponse
    {
        $migrant = Migrant::with([
            'beneficiary', 'destinationCountry', 'destinations',
            'documents', 'financialRecords', 'returnee',
        ])->findOrFail($id);

        return response()->json($migrant);
    }

    public function update(UpdateMigrantRequest $request, $id): JsonResponse
    {
        $migrant = Migrant::findOrFail($id);
        $migrant->update($request->validated());
        $migrant->load('beneficiary', 'destinationCountry');

        return response()->json($migrant);
    }

    public function destroy($id): JsonResponse
    {
        $migrant = Migrant::findOrFail($id);
        $migrant->delete();

        return response()->json(null, 204);
    }

    public function documents($id): JsonResponse
    {
        $migrant = Migrant::findOrFail($id);

        return response()->json($migrant->documents);
    }

    public function storeDocument(Request $request, $id): JsonResponse
    {
        $migrant = Migrant::findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'file_path' => 'required|string|max:255',
            'expiry_date' => 'nullable|date',
        ]);

        $document = $migrant->documents()->create($validated);

        return response()->json($document, 201);
    }

    public function financialRecords($id): JsonResponse
    {
        $migrant = Migrant::findOrFail($id);

        return response()->json($migrant->financialRecords);
    }

    public function storeFinancialRecord(Request $request, $id): JsonResponse
    {
        $migrant = Migrant::findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
        ]);

        $record = $migrant->financialRecords()->create($validated);

        return response()->json($record, 201);
    }

    public function destinations($id): JsonResponse
    {
        $migrant = Migrant::findOrFail($id);

        return response()->json($migrant->destinations);
    }

    public function storeDestination(Request $request, $id): JsonResponse
    {
        $migrant = Migrant::findOrFail($id);

        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'city' => 'nullable|string|max:255',
            'employer_name' => 'nullable|string|max:255',
            'employer_contact' => 'nullable|string|max:50',
            'contract_start' => 'nullable|date',
            'contract_end' => 'nullable|date|after_or_equal:contract_start',
            'salary_amount' => 'nullable|numeric|min:0',
            'salary_currency' => 'nullable|string|max:10',
            'status' => 'sometimes|in:pending,active,completed',
        ]);

        $destination = $migrant->destinations()->create($validated);

        return response()->json($destination, 201);
    }
}
