<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * Human-readable labels for every auditable subject type.
     *
     * Legacy subjects are kept so old activity remains readable without
     * surfacing stale module names.
     */
    private const SUBJECT_LABELS = [
        User::class => 'User Profile',
        UserSkill::class => 'User Skill',
        UserMigrationHistory::class => 'Migration History',
        UserDocument::class => 'User Document',
        Branch::class => 'Branch',
        Staff::class => 'Staff Member',
        StaffIncentive::class => 'Staff Incentive',
        Designation::class => 'Designation',
        Setting::class => 'Setting',
        Course::class => 'Course',
        CourseEnrollment::class => 'Course Enrollment',
        Certificate::class => 'Certificate',
        AwarenessProgram::class => 'Awareness Program',
        AwarenessProgramOutcome::class => 'Awareness Program Outcome',
        ImmediateSupport::class => 'Immediate Support',
        SupportFollowUp::class => 'Support Follow-Up',
        JobBoardInquiry::class => 'Job Board Inquiry',
        Account::class => 'Account',
        JournalEntry::class => 'Journal Entry',
        JournalLine::class => 'Journal Line',
        Report::class => 'Report',
        'Spatie\Permission\Models\Role' => 'Role',
        'Spatie\Permission\Models\Permission' => 'Permission',

        // Archived subjects from retired modules.
        'App\Models\Beneficiary' => 'Legacy Profile',
        'App\Models\BeneficiaryFollowUp' => 'Legacy Profile Follow-Up',
        'App\Models\Migrant' => 'Legacy Migrant Record',
        'App\Models\MigrantDestination' => 'Legacy Migrant Destination',
        'App\Models\Returnee' => 'Legacy Returnee Record',
        'App\Models\ReturneeLivelihoodSupport' => 'Legacy Livelihood Support',
    ];

    private const ACTION_LABELS = [
        'created' => 'Created',
        'updated' => 'Updated',
        'update' => 'Updated',
        'deleted' => 'Deleted',
        'contract_signed' => 'Contract signed',
        'follow_up_scheduled' => 'Follow-up scheduled',
        'livelihood_grant_disbursed' => 'Livelihood grant disbursed',
    ];

    protected $fillable = ['user_id', 'action', 'subject_type', 'subject_id', 'old_values', 'new_values', 'ip_address'];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function subjectLabelFor(?string $type): string
    {
        if (! $type) {
            return 'Unknown';
        }

        return self::SUBJECT_LABELS[$type] ?? Str::headline(class_basename($type));
    }

    public static function actionLabelFor(?string $action): string
    {
        if (! $action) {
            return 'Unknown';
        }

        return self::ACTION_LABELS[$action] ?? Str::headline($action);
    }

    public function getSubjectLabelAttribute(): string
    {
        return self::subjectLabelFor($this->subject_type);
    }

    public function getActionLabelAttribute(): string
    {
        return self::actionLabelFor($this->action);
    }

    /**
     * Best available display name for the affected record.
     */
    public function getSubjectNameAttribute(): ?string
    {
        $values = $this->new_values ?: $this->old_values ?: [];

        foreach (['name', 'title', 'employee_id', 'entry_no', 'certificate_number', 'code', 'key', 'email'] as $field) {
            $value = $values[$field] ?? null;

            if (is_scalar($value) && (string) $value !== '') {
                return (string) $value;
            }
        }

        return null;
    }
}
