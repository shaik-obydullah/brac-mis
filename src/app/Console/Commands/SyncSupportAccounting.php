<?php

namespace App\Console\Commands;

use App\Models\ImmediateSupport;
use App\Services\SupportAccountingService;
use Illuminate\Console\Command;

class SyncSupportAccounting extends Command
{
    protected $signature = 'support:sync-accounting';

    protected $description = 'Create or update the journal entries for support records with amounts';

    public function handle(SupportAccountingService $service): int
    {
        $count = 0;

        ImmediateSupport::query()
            ->with('journalEntry')
            ->chunkById(100, function ($supports) use ($service, &$count) {
                foreach ($supports as $support) {
                    $service->sync($support);
                    $count++;
                }
            });

        $this->info("Synced {$count} support records to accounting.");

        return self::SUCCESS;
    }
}
