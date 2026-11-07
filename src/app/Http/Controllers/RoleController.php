<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditService;
use App\Support\PermissionCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected const SYSTEM_ROLES = ['super-admin', 'admin'];

    public function index()
    {
        $roles = Role::withCount(['permissions', 'users'])
            ->orderBy('name')
            ->paginate(15);

        $stats = [
            'roles' => Role::count(),
            'permissions' => PermissionCatalog::total(),
            'assigned_users' => User::has('roles')->count(),
        ];

        return view('roles.index', compact('roles', 'stats'));
    }

    public function create()
    {
        $permissionGroups = PermissionCatalog::groups();
        $selectedPermissions = old('permissions', []);

        return view('roles.create', compact('permissionGroups', 'selectedPermissions'));
    }

    public function store(Request $request)
    {
        $request->merge(['name' => Str::slug((string) $request->input('name'))]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')],
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $validated['name']]);
        $role->syncPermissions($this->activePermissions($validated['permissions'] ?? []));
        app(AuditService::class)->logCreate($role);

        return redirect('/roles')->with('success', 'Role created successfully.');
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        if ($role->name === 'super-admin') {
            return redirect('/roles')->with('error', 'The super-admin role is locked and cannot be edited.');
        }

        $permissionGroups = PermissionCatalog::groups();
        $selectedPermissions = $role->permissions->pluck('name')->all();

        return view('roles.edit', compact('role', 'permissionGroups', 'selectedPermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'super-admin') {
            return redirect()->back()->with('error', 'The super-admin role cannot be edited.');
        }

        $request->merge(['name' => Str::slug((string) $request->input('name'))]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $old = $role->toArray();
        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($this->activePermissions($validated['permissions'] ?? []));
        app(AuditService::class)->logUpdate($role, $old);

        return redirect('/roles')->with('success', 'Role updated successfully.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if (in_array($role->name, self::SYSTEM_ROLES, true)) {
            return redirect('/roles')->with('error', 'System roles (super-admin, admin) cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            return redirect('/roles')->with('error', 'Cannot delete a role that is assigned to users.');
        }

        app(AuditService::class)->logDelete($role);
        $role->delete();

        return redirect('/roles')->with('success', 'Role deleted successfully.');
    }

    public function users()
    {
        $users = User::with('roles')->orderBy('name')->paginate(15);
        $roles = Role::query()
            ->when(! auth()->user()?->isSuperAdmin(), fn ($query) => $query->where('name', '!=', 'super-admin'))
            ->orderBy('name')
            ->get();

        return view('roles.users', compact('users', 'roles'));
    }

    public function assignRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $new = $validated['roles'] ?? [];

        if (! auth()->user()?->isSuperAdmin()) {
            if ($user->isSuperAdmin() || in_array('super-admin', $new, true)) {
                return back()->with('error', 'Only a super admin can assign or change the super-admin role.');
            }
        }

        $old = $user->getRoleNames()->all();
        $user->syncRoles($new);
        app(AuditService::class)->log('updated', $user, ['roles' => $old], ['roles' => $new]);

        return redirect('/roles/users')->with('success', 'Roles for '.$user->name.' updated successfully.');
    }

    /**
     * Keep only permissions that are still part of the active application.
     *
     * @param  array<int, string>  $permissions
     * @return array<int, string>
     */
    protected function activePermissions(array $permissions): array
    {
        return array_values(array_intersect($permissions, PermissionCatalog::names()));
    }
}
