<?php

namespace App\Console\Commands;

use App\Models\StaffIncentive;
use App\Services\StaffIncentiveAccountingService;
use Illuminate\Console\Command;

class SyncStaffIncentiveAccounting extends Command
{
    protected $signature = 'staff-incentives:sync-accounting';

    protected $description = 'Create or update the journal entries for staff incentives with amounts';

    public function handle(StaffIncentiveAccountingService $service): int
    {
        $count = 0;

        StaffIncentive::query()
            ->with('journalEntry', 'staff.user')
            ->chunkById(100, function ($incentives) use ($service, &$count) {
                foreach ($incentives as $incentive) {
                    $service->sync($incentive);
                    $count++;
                }
            });

        $this->info("Synced {$count} staff incentives to accounting.");

        return self::SUCCESS;
    }
}
