<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    private string $permission = 'manage job board';

    /**
     * Roles that should receive the new permission when the migration runs.
     *
     * @var array<int, string>
     */
    private array $roles = ['super-admin', 'admin', 'manager', 'field-officer'];

    public function up(): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');

        if (empty($tableNames)) {
            return;
        }

        $permissionKey = $columnNames['permission_pivot_key'] ?? 'permission_id';
        $roleKey = $columnNames['role_pivot_key'] ?? 'role_id';

        $permissionId = DB::table($tableNames['permissions'])
            ->where('name', $this->permission)
            ->value('id');

        if (! $permissionId) {
            $permissionId = DB::table($tableNames['permissions'])->insertGetId([
                'name' => $this->permission,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $roleIds = DB::table($tableNames['roles'])
            ->whereIn('name', $this->roles)
            ->pluck('id');

        foreach ($roleIds as $roleId) {
            $exists = DB::table($tableNames['role_has_permissions'])
                ->where($roleKey, $roleId)
                ->where($permissionKey, $permissionId)
                ->exists();

            if (! $exists) {
                DB::table($tableNames['role_has_permissions'])->insert([
                    $roleKey => $roleId,
                    $permissionKey => $permissionId,
                ]);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');

        if (empty($tableNames)) {
            return;
        }

        $permissionKey = $columnNames['permission_pivot_key'] ?? 'permission_id';

        $permissionId = DB::table($tableNames['permissions'])
            ->where('name', $this->permission)
            ->value('id');

        if ($permissionId) {
            DB::table($tableNames['role_has_permissions'])
                ->where($permissionKey, $permissionId)
                ->delete();

            DB::table($tableNames['permissions'])
                ->where('id', $permissionId)
                ->delete();
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
