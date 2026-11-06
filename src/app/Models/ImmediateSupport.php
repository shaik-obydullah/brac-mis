<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImmediateSupport extends Model
{
    use HasFactory;

    public const TYPES = [
        'food' => 'Food',
        'bus_ticket' => 'Bus Ticket',
        'shelter' => 'Shelter',
        'mental_psychological' => 'Mental / Psychological Support',
        'physical' => 'Physical Support',
        'financial' => 'Financial Support',
    ];

    public const CATEGORIES = [
        'immediate' => [
            'label' => 'Immediate Support',
            'types' => ['food', 'bus_ticket', 'shelter'],
        ],
        'mental-psychological' => [
            'label' => 'Mental / Psychological Support',
            'types' => ['mental_psychological'],
        ],
        'physical' => [
            'label' => 'Physical Support',
            'types' => ['physical'],
        ],
        'financial' => [
            'label' => 'Financial Support',
            'types' => ['financial'],
        ],
    ];

    public const STATUSES = [
        'pending' => 'Pending',
        'provided' => 'Provided',
        'cancelled' => 'Cancelled',
    ];

    public const PAID_FROM = [
        'cash' => 'Cash in Hand',
        'bank' => 'Bank Account',
    ];

    public static function categoryTypes(string $category): array
    {
        return self::CATEGORIES[$category]['types'] ?? [];
    }

    public static function categoryForType(string $type): ?string
    {
        foreach (self::CATEGORIES as $category => $config) {
            if (in_array($type, $config['types'], true)) {
                return $category;
            }
        }

        return null;
    }

    protected $fillable = [
        'support_type', 'user_id', 'recipient_name', 'recipient_phone',
        'support_date', 'amount', 'paid_from', 'status', 'description', 'notes', 'provided_by',
    ];

    protected function casts(): array
    {
        return [
            'support_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->support_type] ?? ucfirst(str_replace('_', ' ', $this->support_type));
    }

    public function getPaidFromLabelAttribute(): string
    {
        return self::PAID_FROM[$this->paid_from] ?? self::PAID_FROM['cash'];
    }

    public function getRecipientDisplayNameAttribute(): string
    {
        return $this->user?->name ?? $this->recipient_name ?? 'N/A';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function providedBy()
    {
        return $this->belongsTo(User::class, 'provided_by');
    }

    public function followUps()
    {
        return $this->hasMany(SupportFollowUp::class, 'immediate_support_id')
            ->latest('follow_up_date')
            ->latest('id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
