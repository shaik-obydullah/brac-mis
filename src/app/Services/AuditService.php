<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditService
{
    public function log(string $action, Model $subject, ?array $oldValues = null, ?array $newValues = null): AuditLog
    {
        return AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => get_class($subject),
            'subject_id' => $subject->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
        ]);
    }

    public function logCreate(Model $subject): AuditLog
    {
        return $this->log('created', $subject, null, $subject->toArray());
    }

    public function logUpdate(Model $subject, array $oldValues): AuditLog
    {
        return $this->log('updated', $subject, $oldValues, $subject->getChanges());
    }

    public function logDelete(Model $subject): AuditLog
    {
        return $this->log('deleted', $subject, $subject->toArray(), null);
    }
}
