<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSkill;
use App\Services\AuditService;
use Illuminate\Http\Request;

class SkillAccessController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
    ) {}

    public function index(Request $request)
    {
        $search = trim((string) $request->get('search'));

        $users = User::with('branch', 'roles')
            ->withCount('skills')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();

        $selectedUser = null;

        if ($request->filled('user_id')) {
            $selectedUser = User::with('branch', 'roles', 'skills')
                ->find($request->integer('user_id'));
        }

        $totals = [
            'users' => User::count(),
            'with_skills' => User::has('skills')->count(),
            'skills' => UserSkill::count(),
        ];

        return view('skill-access.index', compact('users', 'selectedUser', 'search', 'totals'));
    }

    public function store(Request $request, User $user)
    {
        $validated = $request->validate([
            'skill_name' => 'required|string|max:255',
            'proficiency' => 'required|in:beginner,intermediate,advanced,expert',
            'years_of_experience' => 'nullable|integer|min:0|max:60',
            'certification' => 'nullable|string|max:255',
        ]);

        $skill = $user->skills()->create($validated);
        $this->auditService->logCreate($skill);

        return redirect()->route('skill-access.index', ['user_id' => $user->id])
            ->with('success', 'Skill added successfully.');
    }

    public function destroy(User $user, UserSkill $skill)
    {
        abort_unless($skill->user_id === $user->id, 404);

        $this->auditService->logDelete($skill);
        $skill->delete();

        return redirect()->route('skill-access.index', ['user_id' => $user->id])
            ->with('success', 'Skill removed successfully.');
    }
}
