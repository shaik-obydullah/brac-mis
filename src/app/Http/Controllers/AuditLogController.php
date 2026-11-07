<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($module = $request->get('module')) {
            $query->where('subject_type', $module);
        }

        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        $logs = $query->paginate(50)->withQueryString();

        $modules = AuditLog::query()
            ->select('subject_type')
            ->distinct()
            ->pluck('subject_type')
            ->mapWithKeys(fn ($type) => [$type => AuditLog::subjectLabelFor($type)])
            ->sort()
            ->all();

        $actions = AuditLog::query()
            ->select('action')
            ->distinct()
            ->pluck('action')
            ->mapWithKeys(fn ($action) => [$action => AuditLog::actionLabelFor($action)])
            ->sort()
            ->all();

        return view('audit-logs.index', compact('logs', 'modules', 'actions'));
    }
}
