<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Branch;
use App\Models\Returnee;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class JobBoardController extends Controller
{
    public function index(Request $request)
    {
        $seekers = $this->buildSeekers($request);

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 12;
        $items = $seekers->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $items,
            $seekers->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $districts = Branch::query()->whereNotNull('district')->pluck('district')->unique()->sort()->values();

        return view('job-board.index', [
            'seekers' => $paginator,
            'districts' => $districts,
        ]);
    }

    public function show(string $type, $id)
    {
        if ($type === 'beneficiary') {
            $beneficiary = Beneficiary::with([
                'branch', 'households', 'interventions', 'migrants.destinationCountry', 'migrants.destinations.country',
            ])->findOrFail($id);

            return view('job-board.show', compact('type', 'beneficiary'));
        }

        if ($type === 'returnee') {
            $returnee = Returnee::with([
                'beneficiary.branch', 'migrant.destinationCountry', 'originCountry',
                'skillAssessments', 'reintegrationPlans', 'livelihoodSupport', 'microfinance',
            ])->findOrFail($id);

            return view('job-board.show', compact('type', 'returnee'));
        }

        abort(404);
    }

    private function buildSeekers(Request $request): Collection
    {
        $search = trim($request->get('search', ''));
        $category = $request->get('category', '');
        $gender = $request->get('gender', '');
        $district = $request->get('district', '');
        $occupation = trim($request->get('occupation', ''));

        $seekers = collect();

        if (in_array($category, ['', 'beneficiary'])) {
            $query = Beneficiary::with('branch');

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('brac_id', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('occupation', 'like', "%{$search}%")
                        ->orWhere('address_line_1', 'like', "%{$search}%");
                });
            }

            if ($gender !== '') {
                $query->where('gender', $gender);
            }

            if ($district !== '') {
                $query->whereHas('branch', fn ($q) => $q->where('district', $district));
            }

            if ($occupation !== '') {
                $query->where('occupation', 'like', "%{$occupation}%");
            }

            $query->get()->each(function (Beneficiary $beneficiary) use ($seekers) {
                $seekers->push($this->normalizeBeneficiary($beneficiary));
            });
        }

        if (in_array($category, ['', 'returnee'])) {
            $query = Returnee::with(['beneficiary.branch', 'migrant.destinationCountry', 'skillAssessments']);

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('beneficiary', fn ($qq) => $qq->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('occupation', 'like', "%{$search}%"))
                        ->orWhereHas('migrant', fn ($qq) => $qq->where('name', 'like', "%{$search}%")
                            ->orWhere('occupation', 'like', "%{$search}%")
                            ->orWhere('brac_id', 'like', "%{$search}%"))
                        ->orWhereHas('skillAssessments', fn ($qq) => $qq->where('skill_name', 'like', "%{$search}%"))
                        ->orWhere('return_reason', 'like', "%{$search}%");
                });
            }

            if ($gender !== '') {
                $query->where(function ($q) use ($gender) {
                    $q->whereHas('beneficiary', fn ($qq) => $qq->where('gender', $gender))
                        ->orWhereHas('migrant', fn ($qq) => $qq->where('gender', $gender));
                });
            }

            if ($district !== '') {
                $query->whereHas('beneficiary.branch', fn ($q) => $q->where('district', $district));
            }

            if ($occupation !== '') {
                $query->where(function ($q) use ($occupation) {
                    $q->whereHas('beneficiary', fn ($qq) => $qq->where('occupation', 'like', "%{$occupation}%"))
                        ->orWhereHas('migrant', fn ($qq) => $qq->where('occupation', 'like', "%{$occupation}%"))
                        ->orWhereHas('skillAssessments', fn ($qq) => $qq->where('skill_name', 'like', "%{$occupation}%"));
                });
            }

            $query->get()->each(function (Returnee $returnee) use ($seekers) {
                $seekers->push($this->normalizeReturnee($returnee));
            });
        }

        return $seekers->sortByDesc('updated_at')->values();
    }

    private function normalizeBeneficiary(Beneficiary $beneficiary): array
    {
        return [
            'type' => 'beneficiary',
            'id' => $beneficiary->id,
            'name' => $beneficiary->name,
            'gender' => $beneficiary->gender ?? 'other',
            'age' => $beneficiary->date_of_birth ? \Carbon\Carbon::parse($beneficiary->date_of_birth)->age : null,
            'phone' => $beneficiary->phone,
            'location' => $beneficiary->branch->district ?? $beneficiary->address_line_1 ?? null,
            'occupation' => $beneficiary->occupation,
            'skills' => [],
            'status' => $beneficiary->status,
            'monthly_income' => $beneficiary->monthly_income,
            'brac_id' => $beneficiary->brac_id ?? $beneficiary->id,
            'updated_at' => $beneficiary->updated_at,
        ];
    }

    private function normalizeReturnee(Returnee $returnee): array
    {
        $source = $returnee->migrant ?? $returnee->beneficiary;
        $name = $source?->name ?? $returnee->beneficiary?->name ?? 'Returnee #' . $returnee->id;
        $gender = $source?->gender ?? $returnee->beneficiary?->gender ?? 'other';
        $dob = $source?->date_of_birth ?? $returnee->beneficiary?->date_of_birth;

        return [
            'type' => 'returnee',
            'id' => $returnee->id,
            'name' => $name,
            'gender' => $gender,
            'age' => $dob ? \Carbon\Carbon::parse($dob)->age : null,
            'phone' => $source?->phone ?? $returnee->beneficiary?->phone,
            'location' => $returnee->beneficiary?->branch?->district ?? $returnee->beneficiary?->address_line_1 ?? null,
            'occupation' => $source?->occupation ?? $returnee->beneficiary?->occupation,
            'skills' => $returnee->skillAssessments->pluck('skill_name')->unique()->values(),
            'status' => $returnee->current_status,
            'return_reason' => $returnee->return_reason,
            'origin_country' => $returnee->originCountry?->name ?? $returnee->migrant?->destinationCountry?->name,
            'brac_id' => $source?->brac_id ?? $returnee->id,
            'updated_at' => $returnee->updated_at,
        ];
    }
}
