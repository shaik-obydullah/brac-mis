<?php

namespace App\Jobs;

use App\Models\BeneficiaryFollowUp;
use App\Models\ReturneeFollowUp;
use App\Notifications\FollowUpReminder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendFollowUpReminder implements ShouldQueue
{
    use Queueable;

    public function __construct() {}

    public function handle(): void
    {
        $date = now()->addDay();

        BeneficiaryFollowUp::whereDate('next_date', $date)
            ->with('beneficiary')
            ->lazy()
            ->each(fn ($followUp) => $followUp->beneficiary?->notify(new FollowUpReminder($followUp)));

        ReturneeFollowUp::whereDate('next_date', $date)
            ->with('returnee.beneficiary')
            ->lazy()
            ->each(fn ($followUp) => $followUp->returnee?->beneficiary?->notify(new FollowUpReminder($followUp)));
    }
}
