<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Country;
use App\Models\Migrant;
use App\Services\AuditService;
use Illuminate\Http\Request;

class MigrantController extends Controller
{
    public function index(Request $request)
    {
        $query = Migrant::with('destinationCountry');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brac_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('destination_country_id')) {
            $query->where('destination_country_id', $request->destination_country_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $migrants = $query->latest()->paginate(15);
        $countries = Country::all();

        return view('migrants.index', compact('migrants', 'countries'));
    }

    public function create()
    {
        $countries = Country::all();
        $beneficiaries = Beneficiary::all();

        return view('migrants.create', compact('countries', 'beneficiaries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'brac_id' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'nid_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'passport_number' => 'nullable|string|max:50',
            'origin_district_id' => 'nullable|string|max:255',
            'origin_upazila_id' => 'nullable|string|max:255',
            'destination_country_id' => 'nullable|exists:countries,id',
            'destination_city' => 'nullable|string|max:255',
            'skill_level' => 'nullable|string|max:255',
            'education_level' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'status' => 'nullable|in:registered,pre_departure,deployed,returned,cancelled',
        ]);

        $migrant = Migrant::create($validated);
        app(AuditService::class)->logCreate($migrant);

        return redirect('/migrants')->with('success', 'Migrant created successfully.');
    }

    public function show($id)
    {
        $migrant = Migrant::with([
            'beneficiary', 'destinationCountry', 'destinations', 'documents', 'financialRecords', 'returnee',
        ])->findOrFail($id);

        return view('migrants.show', compact('migrant'));
    }

    public function edit($id)
    {
        $migrant = Migrant::findOrFail($id);
        $countries = Country::all();
        $beneficiaries = Beneficiary::all();

        return view('migrants.edit', compact('migrant', 'countries', 'beneficiaries'));
    }

    public function update(Request $request, $id)
    {
        $migrant = Migrant::findOrFail($id);

        $validated = $request->validate([
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'brac_id' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'nid_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'passport_number' => 'nullable|string|max:50',
            'origin_district_id' => 'nullable|string|max:255',
            'origin_upazila_id' => 'nullable|string|max:255',
            'destination_country_id' => 'nullable|exists:countries,id',
            'destination_city' => 'nullable|string|max:255',
            'skill_level' => 'nullable|string|max:255',
            'education_level' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'status' => 'nullable|in:registered,pre_departure,deployed,returned,cancelled',
        ]);

        $old = $migrant->toArray();
        $migrant->update($validated);
        app(AuditService::class)->logUpdate($migrant, $old);

        return redirect('/migrants')->with('success', 'Migrant updated successfully.');
    }

    public function destroy($id)
    {
        $migrant = Migrant::findOrFail($id);
        app(AuditService::class)->logDelete($migrant);
        $migrant->delete();

        return redirect('/migrants')->with('success', 'Migrant deleted successfully.');
    }
}
