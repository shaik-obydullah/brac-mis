<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;

class JobBoardController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search'));
        $district = (string) $request->get('district');
        $skill = trim((string) $request->get('skill'));

        $seekers = User::query()
            ->potentialCustomers()
            ->with(['branch', 'skills', 'migrationHistories.country'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhereHas('skills', fn ($skills) => $skills->where('skill_name', 'like', "%{$search}%"));
                });
            })
            ->when($district !== '', fn ($query) => $query->whereHas('branch', fn ($branch) => $branch->where('district', $district)))
            ->when($skill !== '', fn ($query) => $query->whereHas('skills', fn ($skills) => $skills->where('skill_name', 'like', "%{$skill}%")))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $districts = Branch::query()->whereNotNull('district')->pluck('district')->unique()->sort()->values();

        return view('job-board.index', [
            'seekers' => $seekers,
            'districts' => $districts,
        ]);
    }

    public function show(User $user)
    {
        abort_unless($user->isPotentialCustomer(), 404);

        $user->load([
            'branch', 'skills', 'migrationHistories.country',
        ]);

        return view('job-board.show', compact('user'));
    }
}
