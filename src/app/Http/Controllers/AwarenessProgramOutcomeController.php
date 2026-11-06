<?php

namespace App\Http\Controllers;

use App\Models\AwarenessProgram;
use App\Models\AwarenessProgramOutcome;
use App\Services\AuditService;
use Illuminate\Http\Request;

class AwarenessProgramOutcomeController extends Controller
{
    public function index(Request $request)
    {
        $query = AwarenessProgramOutcome::with('awarenessProgram');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query->where('participant_name', 'like', "%{$search}%")
                    ->orWhere('participant_phone', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('awarenessProgram', fn ($program) => $program->where('program_name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('understood')) {
            $query->where('understood', $request->boolean('understood'));
        }

        if ($request->filled('program')) {
            $query->where('awareness_program_id', $request->program);
        }

        $total = (clone $query)->count();
        $understood = (clone $query)->where('understood', true)->count();

        $totals = [
            'total' => $total,
            'understood' => $understood,
            'not_understood' => $total - $understood,
            'understood_percentage' => $total > 0 ? (int) round(($understood / $total) * 100) : 0,
        ];

        $outcomes = $query->latest('id')->paginate(20)->withQueryString();
        $programs = AwarenessProgram::orderBy('program_name')->get(['id', 'program_name']);

        return view('awareness-outcomes.index', compact('outcomes', 'totals', 'programs'));
    }

    public function store(Request $request, AwarenessProgram $awarenessProgram)
    {
        $validated = $this->validateOutcome($request);
        $validated['understood'] = $request->boolean('understood');

        $outcome = $awarenessProgram->outcomes()->create($validated);
        app(AuditService::class)->logCreate($outcome);

        return redirect()->route('awareness-programs.show', $awarenessProgram)
            ->with('success', 'Participant outcome added successfully.');
    }

    public function update(Request $request, AwarenessProgram $awarenessProgram, AwarenessProgramOutcome $outcome)
    {
        abort_unless($outcome->awareness_program_id === $awarenessProgram->id, 404);

        $validated = $this->validateOutcome($request);
        $validated['understood'] = $request->boolean('understood');

        $old = $outcome->toArray();
        $outcome->update($validated);
        app(AuditService::class)->logUpdate($outcome, $old);

        return redirect()->route('awareness-programs.show', $awarenessProgram)
            ->with('success', 'Participant outcome updated successfully.');
    }

    public function destroy(AwarenessProgram $awarenessProgram, AwarenessProgramOutcome $outcome)
    {
        abort_unless($outcome->awareness_program_id === $awarenessProgram->id, 404);

        app(AuditService::class)->logDelete($outcome);
        $outcome->delete();

        return redirect()->route('awareness-programs.show', $awarenessProgram)
            ->with('success', 'Participant outcome removed successfully.');
    }

    private function validateOutcome(Request $request): array
    {
        return $request->validate([
            'participant_name' => 'required|string|max:255',
            'participant_phone' => 'nullable|string|max:20',
            'understood' => 'sometimes|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);
    }
}
