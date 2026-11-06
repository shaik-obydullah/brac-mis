<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Country;
use App\Models\User;
use App\Models\UserDocument;
use App\Models\UserMigrationHistory;
use App\Models\UserSkill;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
    ) {}

    public function index(Request $request)
    {
        $query = User::with('branch', 'roles');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('profile_type') && array_key_exists($request->profile_type, User::PROFILE_TYPES)) {
            $query->where('profile_type', $request->profile_type);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $users = $query->latest('id')->paginate(15)->withQueryString();
        $branches = Branch::orderBy('name')->get();

        return view('users.index', compact('users', 'branches'));
    }

    public function create()
    {
        $branches = Branch::orderBy('name')->get();

        return view('users.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:active,inactive',
            'profile_type' => ['nullable', Rule::in(array_keys(User::PROFILE_TYPES))],
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'status' => $validated['status'],
            'profile_type' => $validated['profile_type'] ?? User::PROFILE_GENERAL_USER,
            'branch_id' => $validated['branch_id'] ?? null,
        ]);

        $this->auditService->logCreate($user);

        return redirect()->route('users.show', $user)->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load('branch', 'roles', 'skills', 'migrationHistories.country', 'documents');
        $countries = Country::orderBy('name')->get();

        return view('users.show', compact('user', 'countries'));
    }

    public function edit(User $user)
    {
        $user->load('roles');
        $branches = Branch::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('users.edit', compact('user', 'branches', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'required|in:active,inactive',
            'profile_type' => ['required', Rule::in(array_keys(User::PROFILE_TYPES))],
            'branch_id' => 'nullable|exists:branches,id',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $old = $user->toArray();
        $oldRoles = $user->getRoleNames()->all();

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'],
            'profile_type' => $validated['profile_type'],
            'branch_id' => $validated['branch_id'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);
        $user->syncRoles($validated['roles'] ?? []);
        $this->auditService->logUpdate($user, $old);

        $newRoles = $user->getRoleNames()->all();
        if ($oldRoles !== $newRoles) {
            $this->auditService->log('update', $user, ['roles' => $oldRoles], ['roles' => $newRoles]);
        }

        return redirect()->route('users.show', $user)->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $this->auditService->logDelete($user);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function storeSkill(Request $request, User $user)
    {
        $validated = $request->validate([
            'skill_name' => 'required|string|max:255',
            'proficiency' => 'required|in:beginner,intermediate,advanced,expert',
            'years_of_experience' => 'nullable|integer|min:0|max:60',
            'certification' => 'nullable|string|max:255',
        ]);

        $skill = $user->skills()->create($validated);
        $this->auditService->logCreate($skill);

        return redirect()->route('users.show', $user)->with('success', 'Skill added successfully.');
    }

    public function destroySkill(User $user, UserSkill $skill)
    {
        abort_unless($skill->user_id === $user->id, 404);

        $this->auditService->logDelete($skill);
        $skill->delete();

        return redirect()->route('users.show', $user)->with('success', 'Skill removed successfully.');
    }

    public function storeMigrationHistory(Request $request, User $user)
    {
        $validated = $request->validate([
            'country_id' => 'nullable|exists:countries,id',
            'employer_name' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'visa_type' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:ongoing,completed,returned',
            'notes' => 'nullable|string|max:2000',
        ]);

        $history = $user->migrationHistories()->create($validated);
        $this->auditService->logCreate($history);

        return redirect()->route('users.show', $user)->with('success', 'Migration history added successfully.');
    }

    public function destroyMigrationHistory(User $user, UserMigrationHistory $history)
    {
        abort_unless($history->user_id === $user->id, 404);

        $this->auditService->logDelete($history);
        $history->delete();

        return redirect()->route('users.show', $user)->with('success', 'Migration history removed successfully.');
    }

    public function storeDocument(Request $request, User $user)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'expiry_date' => 'nullable|date',
        ]);

        $path = $request->file('file')->store("documents/users/{$user->id}", 'public');

        $document = $user->documents()->create([
            'type' => $validated['type'],
            'file_path' => $path,
            'expiry_date' => $validated['expiry_date'] ?? null,
        ]);

        $this->auditService->logCreate($document);

        return redirect()->route('users.show', $user)->with('success', 'Document uploaded successfully.');
    }

    public function destroyDocument(User $user, UserDocument $document)
    {
        abort_unless($document->user_id === $user->id, 404);

        $this->auditService->logDelete($document);
        $document->delete();

        return redirect()->route('users.show', $user)->with('success', 'Document deleted successfully.');
    }
}
