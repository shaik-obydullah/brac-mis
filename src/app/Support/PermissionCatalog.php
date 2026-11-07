<?php

namespace App\Support;

use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class PermissionCatalog
{
    /**
     * Preferred module order and human-readable labels.
     */
    public const MODULE_LABELS = [
        'branches' => 'Branches',
        'awareness programs' => 'Awareness Programs',
        'immediate support' => 'Immediate Support',
        'courses' => 'Courses',
        'certificates' => 'Certificates',
        'staff' => 'Staff',
        'designations' => 'Designations',
        'staff incentives' => 'Staff Incentives',
        'job board' => 'Job Board',
        'finances' => 'Finance',
        'reports' => 'Reports',
        'users' => 'Profiles & Users',
        'roles' => 'Roles',
        'permissions' => 'Permissions',
        'settings' => 'Settings',
        'audit logs' => 'Audit Logs',
    ];

    /**
     * Modules that no longer exist in the application. Any permission that
     * targets one of these modules is hidden from the role editor.
     */
    private const RETIRED_MODULES = [
        'beneficiaries',
        'migrants',
        'returnees',
    ];

    /**
     * Actions are ordered so each module lists View, Create, Edit, Delete first.
     */
    private const ACTION_ORDER = ['view', 'create', 'edit', 'delete', 'export', 'manage'];

    private const ACTION_LABELS = [
        'view' => 'View',
        'create' => 'Create',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'export' => 'Export',
        'manage' => 'Manage',
    ];

    /**
     * @return array<int, array{key: string, label: string, permissions: array<int, array{name: string, action: string, label: string}>}>
     */
    public static function groups(): array
    {
        $groups = [];

        foreach (Permission::query()->orderBy('name')->get() as $permission) {
            [$action, $module] = self::split($permission->name);

            if (self::isRetired($module)) {
                continue;
            }

            $groups[$module]['permissions'][] = [
                'name' => $permission->name,
                'action' => $action,
                'label' => self::actionLabel($action),
            ];
        }

        $ordered = [];

        foreach (array_keys(self::MODULE_LABELS) as $module) {
            if (isset($groups[$module])) {
                $ordered[$module] = $groups[$module];
                unset($groups[$module]);
            }
        }

        // Keep any future module that is not in the preferred order.
        foreach ($groups as $module => $group) {
            $ordered[$module] = $group;
        }

        $result = [];

        foreach ($ordered as $module => $group) {
            $permissions = $group['permissions'];

            usort($permissions, function (array $a, array $b): int {
                return [self::actionPosition($a['action']), $a['name']]
                    <=> [self::actionPosition($b['action']), $b['name']];
            });

            $result[] = [
                'key' => $module,
                'label' => self::moduleLabel($module),
                'permissions' => $permissions,
            ];
        }

        return $result;
    }

    /**
     * Flat list of active permission names shown in the role editor.
     *
     * @return array<int, string>
     */
    public static function names(): array
    {
        return array_values(array_merge(...array_map(
            static fn (array $group): array => array_column($group['permissions'], 'name'),
            self::groups()
        )));
    }

    /**
     * Number of active permissions shown in the role editor.
     */
    public static function total(): int
    {
        return count(self::names());
    }

    public static function moduleLabel(string $module): string
    {
        return self::MODULE_LABELS[$module] ?? Str::headline($module);
    }

    private static function isRetired(string $module): bool
    {
        return in_array($module, self::RETIRED_MODULES, true);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private static function split(string $permission): array
    {
        $parts = explode(' ', $permission, 2);

        return [$parts[0], $parts[1] ?? $permission];
    }

    private static function actionLabel(string $action): string
    {
        return self::ACTION_LABELS[$action] ?? Str::headline($action);
    }

    private static function actionPosition(string $action): int
    {
        $position = array_search($action, self::ACTION_ORDER, true);

        return $position === false ? count(self::ACTION_ORDER) : $position;
    }
}
