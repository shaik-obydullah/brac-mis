<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Country;
use App\Models\Migrant;
use App\Models\Returnee;
use App\Services\AuditService;
use Illuminate\Http\Request;

class ReturneeController extends Controller
{
    public function index(Request $request)
    {
        $query = Returnee::with('migrant', 'beneficiary', 'originCountry');

        if ($search = $request->get('search')) {
            $query->whereHas('migrant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brac_id', 'like', "%{$search}%");
            })->orWhereHas('beneficiary', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('origin_country_id')) {
            $query->where('origin_country_id', $request->origin_country_id);
        }

        if ($request->filled('current_status')) {
            $query->where('current_status', $request->current_status);
        }

        $returnees = $query->latest()->paginate(15);
        $countries = Country::all();

        return view('returnees.index', compact('returnees', 'countries'));
    }

    public function create()
    {
        $countries = Country::all();
        $migrants = Migrant::all();
        $beneficiaries = Beneficiary::all();

        return view('returnees.create', compact('countries', 'migrants', 'beneficiaries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'migrant_id' => 'nullable|exists:migrants,id',
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'return_date' => 'nullable|date',
            'return_reason' => 'nullable|string',
            'origin_country_id' => 'nullable|exists:countries,id',
            'current_status' => 'nullable|in:assessed,planning,in_progress,completed,dropped',
        ]);

        $returnee = Returnee::create($validated);
        app(AuditService::class)->logCreate($returnee);

        return redirect('/returnees')->with('success', 'Returnee created successfully.');
    }

    public function show($id)
    {
        $returnee = Returnee::with([
            'migrant', 'beneficiary', 'originCountry', 'reintegrationPlans',
            'skillAssessments', 'livelihoodSupport', 'microfinance', 'followUps',
        ])->findOrFail($id);

        return view('returnees.show', compact('returnee'));
    }

    public function edit($id)
    {
        $returnee = Returnee::findOrFail($id);
        $countries = Country::all();
        $migrants = Migrant::all();
        $beneficiaries = Beneficiary::all();

        return view('returnees.edit', compact('returnee', 'countries', 'migrants', 'beneficiaries'));
    }

    public function update(Request $request, $id)
    {
        $returnee = Returnee::findOrFail($id);

        $validated = $request->validate([
            'migrant_id' => 'nullable|exists:migrants,id',
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'return_date' => 'nullable|date',
            'return_reason' => 'nullable|string',
            'origin_country_id' => 'nullable|exists:countries,id',
            'current_status' => 'nullable|in:assessed,planning,in_progress,completed,dropped',
        ]);

        $old = $returnee->toArray();
        $returnee->update($validated);
        app(AuditService::class)->logUpdate($returnee, $old);

        return redirect('/returnees')->with('success', 'Returnee updated successfully.');
    }

    public function destroy($id)
    {
        $returnee = Returnee::findOrFail($id);
        app(AuditService::class)->logDelete($returnee);
        $returnee->delete();

        return redirect('/returnees')->with('success', 'Returnee deleted successfully.');
    }
}
