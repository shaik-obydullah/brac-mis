<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Modules that have been removed from the application.
     *
     * @var array<int, string>
     */
    private array $retiredModules = [
        'beneficiaries',
        'migrants',
        'returnees',
    ];

    public function up(): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');

        if (empty($tableNames)) {
            return;
        }

        $permissionKey = $columnNames['permission_pivot_key'] ?? 'permission_id';

        $names = [];

        foreach ($this->retiredModules as $module) {
            foreach (['view', 'create', 'edit', 'delete', 'export', 'manage'] as $action) {
                $names[] = $action.' '.$module;
            }
        }

        $permissionIds = DB::table($tableNames['permissions'])
            ->whereIn('name', $names)
            ->pluck('id');

        if ($permissionIds->isEmpty()) {
            return;
        }

        DB::table($tableNames['role_has_permissions'])
            ->whereIn($permissionKey, $permissionIds)
            ->delete();

        DB::table($tableNames['model_has_permissions'])
            ->whereIn($permissionKey, $permissionIds)
            ->delete();

        DB::table($tableNames['permissions'])
            ->whereIn('id', $permissionIds)
            ->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // The retired modules no longer exist, so their permissions are not restored.
    }
};
