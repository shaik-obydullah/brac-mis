<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected const SYSTEM_ROLES = ['super-admin', 'admin'];

    public function index()
    {
        $roles = Role::with('permissions')->withCount('users')->orderBy('name')->paginate(15);

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = $this->groupedPermissions();

        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => Str::slug($validated['name'])]);
        $role->syncPermissions($request->input('permissions', []));
        app(AuditService::class)->logCreate($role);

        return redirect('/roles')->with('success', 'Role created successfully.');
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = $this->groupedPermissions();

        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'super-admin') {
            return redirect()->back()->with('error', 'The super-admin role cannot be edited.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $old = $role->toArray();
        $role->update(['name' => Str::slug($validated['name'])]);
        $role->syncPermissions($request->input('permissions', []));
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
        $roles = Role::orderBy('name')->get();

        return view('roles.users', compact('users', 'roles'));
    }

    public function assignRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $old = $user->getRoleNames()->all();
        $new = $validated['roles'] ?? [];
        $user->syncRoles($new);
        app(AuditService::class)->log('update', $user, ['roles' => $old], ['roles' => $new]);

        return redirect('/roles/users')->with('success', 'Roles for ' . $user->name . ' updated successfully.');
    }

    protected function groupedPermissions()
    {
        return Permission::orderBy('name')->get()->groupBy(function (Permission $permission) {
            $parts = explode(' ', $permission->name);

            return end($parts);
        });
    }
}
