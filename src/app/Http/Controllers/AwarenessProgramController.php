<?php

namespace App\Http\Controllers;

use App\Models\AwarenessProgram;
use App\Models\AwarenessProgramOutcome;
use App\Services\AuditService;
use Illuminate\Http\Request;

class AwarenessProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = AwarenessProgram::with('createdBy');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('program_name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('division')) {
            $query->where('division', $request->division);
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        if ($request->filled('upazila')) {
            $query->where('upazila', $request->upazila);
        }

        if ($request->filled('understanding')) {
            match ($request->understanding) {
                'understood' => $query->whereHas('outcomes', fn ($outcome) => $outcome->where('understood', true)),
                'not_understood' => $query->whereHas('outcomes', fn ($outcome) => $outcome->where('understood', false)),
                'not_tested' => $query->doesntHave('outcomes'),
                default => null,
            };
        }

        $totalPrograms = (clone $query)->count();

        $outcomeQuery = AwarenessProgramOutcome::whereIn(
            'awareness_program_id',
            (clone $query)->select('id')
        );

        $totalParticipants = (clone $outcomeQuery)->count();
        $understoodParticipants = (clone $outcomeQuery)->where('understood', true)->count();

        $totals = [
            'programs' => $totalPrograms,
            'participants' => $totalParticipants,
            'understood' => $understoodParticipants,
            'not_understood' => $totalParticipants - $understoodParticipants,
            'understood_percentage' => $totalParticipants > 0
                ? (int) round(($understoodParticipants / $totalParticipants) * 100)
                : 0,
        ];

        $programs = $query
            ->withCount([
                'outcomes',
                'outcomes as understood_outcomes_count' => fn ($outcome) => $outcome->where('understood', true),
            ])
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('awareness-programs.index', compact('programs', 'totals'));
    }

    public function create()
    {
        return view('awareness-programs.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateProgram($request);
        $validated['created_by'] = auth()->id();

        $program = AwarenessProgram::create($validated);
        app(AuditService::class)->logCreate($program);

        return redirect('/awareness-programs')->with('success', 'Awareness program created successfully.');
    }

    public function show($id)
    {
        $program = AwarenessProgram::with(['createdBy', 'outcomes' => fn ($outcomes) => $outcomes->latest('id')])
            ->withCount([
                'outcomes',
                'outcomes as understood_outcomes_count' => fn ($outcome) => $outcome->where('understood', true),
            ])
            ->findOrFail($id);

        return view('awareness-programs.show', compact('program'));
    }

    public function edit($id)
    {
        $program = AwarenessProgram::findOrFail($id);

        return view('awareness-programs.edit', compact('program'));
    }

    public function update(Request $request, $id)
    {
        $program = AwarenessProgram::findOrFail($id);
        $validated = $this->validateProgram($request);

        $old = $program->toArray();
        $program->update($validated);
        app(AuditService::class)->logUpdate($program, $old);

        return redirect('/awareness-programs')->with('success', 'Awareness program updated successfully.');
    }

    public function destroy($id)
    {
        $program = AwarenessProgram::findOrFail($id);

        app(AuditService::class)->logDelete($program);
        $program->delete();

        return redirect('/awareness-programs')->with('success', 'Awareness program deleted successfully.');
    }

    private function validateProgram(Request $request): array
    {
        return $request->validate([
            'program_name' => 'required|string|max:255',
            'division' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'upazila' => 'required|string|max:255',
            'address' => 'nullable|string|max:1000',
        ]);
    }
}
